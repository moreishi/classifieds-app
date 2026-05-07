<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class DiscoveryFeed extends Component
{
    use WithPagination;

    public string $vibe = '';

    protected $queryString = [
        'vibe' => ['except' => ''],
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
            'vibes' => $vibes,
        ])->layout('layouts.app');
    }
}
