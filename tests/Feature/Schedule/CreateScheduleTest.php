<?php

namespace Tests\Feature\Permissions;

use App\Indisposition;
use App\MonthlyRequirments;
use App\Services\Check;
use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Tests\Helpers\SetUper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class CreateScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $setUper = new SetUper();
        $setUper->setUp();
    }
}
