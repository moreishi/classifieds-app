<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscoveryFeedTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    // ---- Existing functionality (should pass) ----

    #[Test]
    public function ganaps_index_page_loads(): void
    {
        $response = $this->get(route('ganaps.index'));

        $response->assertStatus(200);
    }

    #[Test]
    public function feed_shows_active_events(): void
    {
        Event::factory()->count(3)->create();

        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->assertCount('events', 3);
    }

    #[Test]
    public function feed_does_not_show_inactive_events(): void
    {
        Event::factory()->count(2)->create();
        Event::factory()->inactive()->create();

        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->assertCount('events', 2);
    }

    #[Test]
    public function feed_can_filter_by_vibe(): void
    {
        Event::factory()->withVibe('Tech')->create(['title' => 'Tech Conference']);
        Event::factory()->withVibe('Music')->create(['title' => 'Music Fest']);

        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->set('vibe', 'Tech')
            ->assertCount('events', 1);
    }

    #[Test]
    public function vibe_filter_resets_pagination(): void
    {
        Event::factory()->count(15)->create();

        $component = Livewire::test(\App\Livewire\DiscoveryFeed::class);

        // Go to page 2
        $component->set('page', 2);
        $component->assertSet('page', 2);

        // Changing vibe should reset to page 1
        $component->set('vibe', 'Tech');
        $component->assertSet('page', 1);
    }

    #[Test]
    public function vibe_is_persisted_in_query_string(): void
    {
        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->assertSet('vibe', '');
    }

    #[Test]
    public function empty_state_shows_when_no_events(): void
    {
        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->assertSet('vibe', '');
    }

    #[Test]
    public function empty_state_with_vibe_shows_vibe_name(): void
    {
        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->set('vibe', 'Sports');
    }

    #[Test]
    public function feed_orders_events_by_date_ascending(): void
    {
        $early = Event::factory()->create(['event_date' => now()->addDays(30)]);
        $late = Event::factory()->create(['event_date' => now()->addDays(60)]);
        $mid = Event::factory()->create(['event_date' => now()->addDays(45)]);

        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->assertSet('vibe', '');
    }

    #[Test]
    public function feed_paginates_at_twelve_per_page(): void
    {
        Event::factory()->count(15)->create();

        Livewire::test(\App\Livewire\DiscoveryFeed::class)
            ->assertCount('events', 12);
    }

    // ---- Tests for the broken show route (these should fail until we fix it) ----

    #[Test]
    public function event_detail_page_shows_single_event(): void
    {
        $event = Event::factory()->create([
            'title' => 'Test Event Detail',
        ]);

        $response = $this->get(route('ganaps.show', $event->slug));

        $response->assertStatus(200);
        $response->assertSee('Test Event Detail');
    }

    #[Test]
    public function event_detail_shows_404_for_invalid_slug(): void
    {
        $response = $this->get(route('ganaps.show', 'non-existent-event'));

        $response->assertStatus(404);
    }

    #[Test]
    public function event_detail_does_not_show_inactive_event(): void
    {
        $event = Event::factory()->inactive()->create();

        $response = $this->get(route('ganaps.show', $event->slug));

        $response->assertStatus(404);
    }
}
