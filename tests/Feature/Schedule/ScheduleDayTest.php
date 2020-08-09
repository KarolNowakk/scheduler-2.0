<?php

namespace Tests\Feature\Permissions;

use App\Indisposition;
use App\MonthlyRequirments;
use App\Services\Check;
use App\Services\ScheduleDay;
use App\Services\ScheduleGenerator;
use App\Services\WorkerShiftCreator;
use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Tests\Helpers\SetUper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ScheduleDayTest extends TestCase
{
    use RefreshDatabase;

    protected $setUper;

    public function setUp() : void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->setUper = new SetUper();
        $this->setUper->setUp();
    }

    /** @test */
    public function create_should_create_shift()
    {
        $this->withoutExceptionHandling();
        $schedule = new ScheduleGenerator($this->setUper->getWorkPlace(), '2020-12-12', $this->setUper->getMonth());

        $schedule->generate();

        dd(Shift::all()->count());
    }
}
