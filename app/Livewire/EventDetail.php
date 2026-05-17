<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventDetail extends Component
{
    public Event $event;

    public function mount(string $slug): void
    {
        $this->event = Event::active()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.event-detail', [
            'event' => $this->event,
        ])->layout('layouts.app');
    }
}
