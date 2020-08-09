<?php

namespace Tests\Feature\Permissions;

use App\Indisposition;
use App\MonthlyRequirments;
use App\Services\Check;
use App\Services\ScheduleGenerator;
use App\Shift;
use App\User;
use App\Worker;
use App\WorkPlace;
use Tests\Helpers\SetUper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ScheduleGeneratorTest extends TestCase
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
        $schedule = new ScheduleGenerator($this->setUper->getWorkPlace(), $this->setUper->getMonth());

        $schedule->generate();

        dd(Shift::all()->count(), $this->setUper->getWorker()->shifts);
    }
}
