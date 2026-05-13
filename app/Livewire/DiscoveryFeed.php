<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\LiveBeacon;
use Livewire\Component;
use Livewire\WithPagination;

class DiscoveryFeed extends Component
{
    use WithPagination;

    public string $vibe = '';

    protected $queryString = [
        'vibe' => ['except' => ''],
    ];

    protected $listeners = [
        'beacon-started' => '$refresh',
        'beacon-ended' => '$refresh',
    ];

    public function updatingVibe(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $events = Event::active()
            ->byVibe($this->vibe)
            ->orderBy('event_date')
            ->paginate(12);

        $liveBeacons = LiveBeacon::with('user')
            ->active()
            ->latest('started_at')
            ->get();

        $vibes = [
            'Party',
            'Hustle',
            'Art',
            'Tech',
            'Music',
            'Food',
            'Sports',
            'Community',
        ];

        return view('livewire.discovery-feed', [
            'events' => $events,
            'liveBeacons' => $liveBeacons,
            'vibes' => $vibes,
        ])->layout('layouts.app');
    }
}
