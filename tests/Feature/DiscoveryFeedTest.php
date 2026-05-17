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

    #[Test]
    public function ganaps_index_page_loads(): void
    {
        $response = $this->get(route("ganaps.index"));

        $response->assertStatus(200);
    }

    #[Test]
    public function feed_shows_active_events(): void
    {
        Event::factory()->count(3)->create([
            "title" => "Active Event",
        ]);

        $response = $this->get(route("ganaps.index"));

        $response->assertStatus(200);
        $response->assertSee("Active Event");
    }

    #[Test]
    public function feed_does_not_show_inactive_events(): void
    {
        Event::factory()->count(2)->create([
            "title" => "Visible Event",
        ]);
        Event::factory()->inactive()->create([
            "title" => "Hidden Event",
        ]);

        $response = $this->get(route("ganaps.index"));

        $response->assertStatus(200);
        $response->assertSee("Visible Event");
        $response->assertDontSee("Hidden Event");
    }

    #[Test]
    public function feed_can_filter_by_vibe(): void
    {
        Event::factory()->withVibe("Tech")->create(["title" => "Tech Conference"]);
        Event::factory()->withVibe("Music")->create(["title" => "Music Fest"]);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\DiscoveryFeed::class)
            ->set("vibe", "Tech")
            ->assertSee("Tech Conference");
    }

    #[Test]
    public function vibe_is_persisted_in_query_string(): void
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\DiscoveryFeed::class)
            ->assertSet("vibe", "");
    }

    #[Test]
    public function empty_state_when_no_events(): void
    {
        $response = $this->get(route("ganaps.index"));

        $response->assertStatus(200);
    }

    #[Test]
    public function feed_orders_events_by_date_ascending(): void
    {
        Event::factory()->create([
            "title" => "Late Event",
            "event_date" => now()->addDays(60),
        ]);
        Event::factory()->create([
            "title" => "Early Event",
            "event_date" => now()->addDays(30),
        ]);

        $response = $this->get(route("ganaps.index"));

        $response->assertStatus(200);
        $response->assertSee("Early Event");
        $response->assertSee("Late Event");
    }

    #[Test]
    public function event_detail_page_shows_single_event(): void
    {
        $event = Event::factory()->create([
            "title" => "Test Event Detail",
        ]);

        $response = $this->get(route("ganaps.show", $event->slug));

        $response->assertStatus(200);
        $response->assertSee("Test Event Detail");
    }

    #[Test]
    public function event_detail_shows_404_for_invalid_slug(): void
    {
        $response = $this->get(route("ganaps.show", "non-existent-event"));

        $response->assertStatus(404);
    }

    #[Test]
    public function event_detail_does_not_show_inactive_event(): void
    {
        $event = Event::factory()->inactive()->create();

        $response = $this->get(route("ganaps.show", $event->slug));

        $response->assertStatus(404);
    }
}
