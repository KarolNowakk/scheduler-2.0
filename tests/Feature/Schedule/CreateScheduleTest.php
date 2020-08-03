<?php

namespace Tests\Feature\Permissions;

use App\Indisposition;
use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Tests\Helpers\SetUper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function set_up_test()
    {
        $this->withoutExceptionHandling();
        $setUper = new SetUper();
        $setUper->setUp();

        dd(Indisposition::all()->count());
    }
}
