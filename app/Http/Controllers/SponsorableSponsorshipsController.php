<?php

namespace App\Http\Controllers;

use App\Sponsorable;

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
}
