<?php

namespace App\Http\Controllers;

use App\Sponsorable;
use App\SponsorableSlot;
use App\Sponsorship;

class SponsorableSponsorshipsController extends Controller
{
    function new ($slug) {
        $sponsorable = Sponsorable::findOrFailBySlug($slug);

        $sponsorableSlots = $sponsorable->slots()->sponsorable()->orderBy('publish_date')->get();

        return view('sponsorable-sponsorships.new', [
            'sponsorable' => $sponsorable,
            'sponsorableSlots' => $sponsorableSlots,
        ]);
    }

    public function store()
    {
        $sponsorship = Sponsorship::create();

        $slots = SponsorableSlot::whereIn('id', request('sponsorable_slots'))->get();

        $slots->each->update(['sponsorship_id' => $sponsorship->id]);

        return response()->json([], 201);
    }
}
