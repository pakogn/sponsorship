<?php

namespace Tests\Feature;

use App\Sponsorable;
use App\SponsorableSlot;
use App\Sponsorship;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewNewSponsorshipPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_viewing_the_new_sponsorship_page()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $sponsorableSlots = new EloquentCollection([
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2)]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3)]),
        ]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $sponsorableSlots->assertEquals($response->data('sponsorableSlots'));
    }

    public function test_sponsorable_slots_are_listed_in_chronological_order()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slotA = factory(SponsorableSlot::class)->create(['publish_date' => now()->addDays(10), 'sponsorable_id' => $sponsorable]);
        $slotB = factory(SponsorableSlot::class)->create(['publish_date' => now()->addDays(30), 'sponsorable_id' => $sponsorable]);
        $slotC = factory(SponsorableSlot::class)->create(['publish_date' => now()->addDays(3), 'sponsorable_id' => $sponsorable]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(3, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotC));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotA));
        $this->assertTrue($response->data('sponsorableSlots')[2]->is($slotB));
    }

    public function test_only_upcoming_sponsorable_slots_are_listed()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $slotA = factory(SponsorableSlot::class)->create(['publish_date' => now()->subDays(10), 'sponsorable_id' => $sponsorable]);
        $slotB = factory(SponsorableSlot::class)->create(['publish_date' => now()->subDays(1), 'sponsorable_id' => $sponsorable]);
        $slotC = factory(SponsorableSlot::class)->create(['publish_date' => now()->addDays(1), 'sponsorable_id' => $sponsorable]);
        $slotD = factory(SponsorableSlot::class)->create(['publish_date' => now()->addDays(10), 'sponsorable_id' => $sponsorable]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(2, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotC));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotD));
    }

    public function test_only_purchasable_sponsorable_slots_are_listed()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);
        $sponsorship = factory(Sponsorship::class)->create();

        $slotA = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(1)]);
        $slotB = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(2), 'sponsorship_id' => $sponsorship]);
        $slotC = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(3), 'sponsorship_id' => $sponsorship]);
        $slotD = factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable, 'publish_date' => now()->addMonths(4)]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(2, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotA));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotD));
    }
}
