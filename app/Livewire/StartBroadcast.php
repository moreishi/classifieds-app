<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\LiveBeacon;
use Livewire\Component;

class StartBroadcast extends Component
{
    public string $step = 'capture';
    public string $capturedPhoto = '';
    public string $description = '';
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?int $cityId = null;
    public string $locationName = '';
    public string $gpsStatus = 'detecting';
    public string $error = '';
    public bool $showModal = false;

    protected function rules(): array
    {
        return [
            'description' => 'required|string|max:200',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'cityId' => 'nullable|exists:cities,id',
        ];
    }

    public function open(): void
    {
        $this->resetExcept('showModal');
        $this->showModal = true;
        $this->step = 'capture';
        $this->gpsStatus = 'detecting';
    }

    public function close(): void
    {
        $this->reset();
    }

    public function setPhoto(string $photoData): void
    {
        $decoded = base64_decode(explode(',', $photoData)[1] ?? '');
        if (empty($decoded)) {
            $this->error = 'Failed to capture photo.';
            return;
        }

        $this->capturedPhoto = $photoData;
        $this->error = '';
        $this->step = 'compose';
    }

    public function captureGps(float $lat, float $lng, string $locationName = ''): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->locationName = $locationName;
        $this->gpsStatus = 'captured';
    }

    public function gpsDenied(): void
    {
        $this->gpsStatus = 'denied';
    }

    public function gpsUnavailable(): void
    {
        $this->gpsStatus = 'unavailable';
    }

    public function startBroadcast(): void
    {
        $this->validate();

        $user = auth()->user();

        // Enforce one active beacon per user
        if (LiveBeacon::where('user_id', $user->id)->where('status', 'live')->exists()) {
            $this->error = 'You already have an active live session. Please end it first.';
            return;
        }

        $decoded = base64_decode(explode(',', $this->capturedPhoto)[1] ?? '');
        if (empty($decoded)) {
            $this->error = 'Photo data is invalid. Please capture again.';
            return;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'beacon_') . '.jpg';
        file_put_contents($tmpPath, $decoded);

        $beacon = LiveBeacon::create([
            'user_id' => $user->id,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_name' => $this->locationName,
            'city_id' => $this->cityId,
            'status' => 'live',
        ]);

        $beacon
            ->addMedia($tmpPath)
            ->usingName('snapshot.jpg')
            ->toMediaCollection('snapshot');

        @unlink($tmpPath);

        $this->step = 'done';
        $this->dispatch('beacon-started');
    }

    public function endBroadcast(): void
    {
        $beacon = LiveBeacon::where('user_id', auth()->id())
            ->where('status', 'live')
            ->first();

        $beacon?->end();

        $this->close();
        $this->dispatch('beacon-ended');
    }

    public function goToFeed(): void
    {
        $this->close();
        $this->redirect(route('ganaps.index'));
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.start-broadcast', [
            'hasActiveBeacon' => LiveBeacon::where('user_id', $user?->id)
                ->where('status', 'live')
                ->exists(),
            'activeBeacon' => LiveBeacon::where('user_id', $user?->id)
                ->where('status', 'live')
                ->first(),
            'provinces' => City::with('children')
                ->where('is_active', true)
                ->where('type', 'province')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
