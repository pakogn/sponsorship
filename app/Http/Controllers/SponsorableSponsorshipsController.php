<?php

namespace App\Http\Controllers;

use App\Sponsorable;

class SponsorableSponsorshipsController extends Controller
{
    function new ($slug) {
        $sponsorable = Sponsorable::findOrFailBySlug($slug);

        $sponsorableSlots = $sponsorable->slots;

        return view('sponsorable-sponsorships.new', [
            'sponsorable' => $sponsorable,
            'sponsorableSlots' => $sponsorableSlots,
        ]);
    }
}
