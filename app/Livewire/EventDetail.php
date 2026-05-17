<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventDetail extends Component
{
    public Event $event;

    public function mount(string $slug): void
    {
        $event = Event::active()
            ->where('slug', $slug)
            ->first();

        if (!$event) {
            abort(404);
        }

        $this->event = $event;
    }

    public function render()
    {
        return view('livewire.event-detail', [
            'event' => $this->event,
        ])->layout('layouts.app');
    }
}
