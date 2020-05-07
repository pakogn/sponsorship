<?php

namespace Tests\Feature;

use App\Sponsorable;
use App\SponsorableSlot;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class NewSponsorshipTest extends TestCase
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
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
            factory(SponsorableSlot::class)->create(['sponsorable_id' => $sponsorable]),
        ]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $sponsorableSlots->assertEquals($response->data('sponsorableSlots'));
    }

    public function test_sponsorable_slots_are_listed_in_chronological_order()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $sponsorableSlots = new EloquentCollection([
            $slotA = factory(SponsorableSlot::class)->create(['publish_date' => Carbon::now()->addDays(10), 'sponsorable_id' => $sponsorable]),
            $slotB = factory(SponsorableSlot::class)->create(['publish_date' => Carbon::now()->addDays(30), 'sponsorable_id' => $sponsorable]),
            $slotC = factory(SponsorableSlot::class)->create(['publish_date' => Carbon::now()->addDays(3), 'sponsorable_id' => $sponsorable]),
        ]);

        $response = $this->get('/full-stack-radio/sponsorships/new');

        $response->assertSuccessful();
        $this->assertTrue($response->data('sponsorable')->is($sponsorable));
        $this->assertCount(3, $response->data('sponsorableSlots'));
        $this->assertTrue($response->data('sponsorableSlots')[0]->is($slotC));
        $this->assertTrue($response->data('sponsorableSlots')[1]->is($slotA));
        $this->assertTrue($response->data('sponsorableSlots')[2]->is($slotB));
    }
}
