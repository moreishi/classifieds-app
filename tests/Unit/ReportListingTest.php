<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\Report;
use App\Models\User;
use App\Livewire\ReportListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportListingTest extends TestCase
{
    use RefreshDatabase;

    private User $reporter;
    private User $seller;
    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $region = Region::factory()->create();
        $city = City::factory()->for($region)->create(['is_active' => true]);
        $category = Category::factory()->create(['is_active' => true]);

        $this->reporter = User::factory()->create();
        $this->seller = User::factory()->create();

        $this->listing = Listing::factory()->create([
            'user_id' => $this->seller->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'status' => 'active',
        ]);
    }

    #[Test]
    public function guest_cannot_submit_report(): void
    {
        Livewire::test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->call('submit')
            ->assertDispatched('report-needs-login');
    }

    #[Test]
    public function authenticated_user_can_submit_report(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->call('submit')
            ->assertDispatched('report-submitted');

        $this->assertDatabaseHas('reports', [
            'listing_id' => $this->listing->id,
            'reporter_id' => $this->reporter->id,
            'reason' => 'spam',
            'status' => 'open',
        ]);
    }

    #[Test]
    public function report_requires_valid_reason(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'invalid_reason')
            ->call('submit')
            ->assertHasErrors(['reason']);
    }

    #[Test]
    public function report_requires_reason(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->call('submit')
            ->assertHasErrors(['reason']);
    }

    #[Test]
    public function description_is_optional(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->call('submit')
            ->assertDispatched('report-submitted');

        $this->assertDatabaseHas('reports', [
            'listing_id' => $this->listing->id,
            'reporter_id' => $this->reporter->id,
            'description' => null,
        ]);
    }

    #[Test]
    public function description_cannot_exceed_1000_chars(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->set('description', str_repeat('a', 1001))
            ->call('submit')
            ->assertHasErrors(['description']);
    }

    #[Test]
    public function prevents_duplicate_open_reports_from_same_user(): void
    {
        Report::create([
            'listing_id' => $this->listing->id,
            'reporter_id' => $this->reporter->id,
            'reason' => 'spam',
        ]);

        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->call('submit')
            ->assertDispatched('report-error');

        // Only 1 report record in DB
        $this->assertEquals(1, Report::count());
    }

    #[Test]
    public function allows_new_report_after_previous_was_handled(): void
    {
        Report::create([
            'listing_id' => $this->listing->id,
            'reporter_id' => $this->reporter->id,
            'reason' => 'spam',
            'status' => 'dismissed',
            'handled_by' => $this->seller->id,
            'handled_at' => now(),
        ]);

        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'scam')
            ->call('submit')
            ->assertDispatched('report-submitted');

        $this->assertEquals(2, Report::count());
    }

    #[Test]
    public function report_creates_pending_status_by_default(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'scam')
            ->call('submit');

        $report = Report::first();
        $this->assertEquals('open', $report->status);
        $this->assertNull($report->handled_by);
        $this->assertNull($report->handled_at);
    }

    #[Test]
    public function different_users_can_report_same_listing(): void
    {
        $anotherUser = User::factory()->create();

        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->call('submit');

        Livewire::actingAs($anotherUser)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'misleading')
            ->call('submit')
            ->assertDispatched('report-submitted');

        $this->assertEquals(2, Report::count());
    }

    #[Test]
    public function report_resets_form_after_submission(): void
    {
        Livewire::actingAs($this->reporter)
            ->test(ReportListing::class, ['listing' => $this->listing])
            ->set('reason', 'spam')
            ->set('description', 'This is spam')
            ->call('submit')
            ->assertSet('reason', '')
            ->assertSet('description', null)
            ->assertSet('showForm', false);
    }

    #[Test]
    public function all_report_reasons_are_acceptable(): void
    {
        $reasons = ['spam', 'misleading', 'scam', 'prohibited', 'duplicate', 'wrong_category', 'other'];

        foreach ($reasons as $reason) {
            Livewire::actingAs($this->reporter)
                ->test(ReportListing::class, ['listing' => $this->listing])
                ->set('reason', $reason)
                ->call('submit');

            $this->assertDatabaseHas('reports', [
                'listing_id' => $this->listing->id,
                'reporter_id' => $this->reporter->id,
                'reason' => $reason,
            ]);

            // Clear for next iteration
            Report::query()->delete();
        }
    }

    #[Test]
    public function report_model_exposes_is_open(): void
    {
        $open = Report::create(['listing_id' => $this->listing->id, 'reporter_id' => $this->reporter->id, 'reason' => 'spam']);
        $this->assertTrue($open->isOpen());

        $dismissed = Report::create(['listing_id' => $this->listing->id, 'reporter_id' => $this->reporter->id, 'reason' => 'spam', 'status' => 'dismissed']);
        $this->assertFalse($dismissed->isOpen());
    }
}
