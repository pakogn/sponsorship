<?php

namespace Tests\Unit;

use App\Sponsorable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SponsorableTest extends TestCase
{
    use RefreshDatabase;

    public function test_finding_a_sponsorable_by_slug()
    {
        $sponsorable = factory(Sponsorable::class)->create(['slug' => 'full-stack-radio']);

        $foundSponsorable = Sponsorable::findOrFailBySlug('full-stack-radio');

        $this->assertTrue($foundSponsorable->is($sponsorable));
    }

    public function test_an_exception_is_thrown_if_a_sponsorable_cannot_be_found_by_slug()
    {
        $this->expectException(ModelNotFoundException::class);
        Sponsorable::findOrFailBySlug('slug-that-does-not-exist');
    }
}
