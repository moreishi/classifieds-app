<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\Report;
use Livewire\Component;

class ReportListing extends Component
{
    public Listing $listing;
    public bool $showForm = false;
    public string $reason = '';
    public ?string $description = null;

    protected array $reasons = [
        'spam' => 'Spam',
        'misleading' => 'Misleading or incorrect information',
        'scam' => 'Suspected scam or fraud',
        'prohibited' => 'Prohibited item or service',
        'duplicate' => 'Duplicate listing',
        'wrong_category' => 'Wrong category',
        'other' => 'Other',
    ];

    protected function rules(): array
    {
        return [
            'reason' => 'required|in:' . implode(',', array_keys($this->reasons)),
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function submit(): void
    {
        if (!auth()->check()) {
            $this->dispatch('report-needs-login');
            return;
        }

        $this->validate();

        // Prevent duplicate reports from same user on same listing
        $existing = Report::where('listing_id', $this->listing->id)
            ->where('reporter_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if ($existing) {
            $this->dispatch('report-error', message: 'You already reported this listing.');
            return;
        }

        Report::create([
            'listing_id' => $this->listing->id,
            'reporter_id' => auth()->id(),
            'reason' => $this->reason,
            'description' => $this->description,
        ]);

        $this->reset(['reason', 'description', 'showForm']);
        $this->dispatch('report-submitted');
    }

    public function render()
    {
        return view('livewire.report-listing', [
            'reasons' => $this->reasons,
        ]);
    }
}
