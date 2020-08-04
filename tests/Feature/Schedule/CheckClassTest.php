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

class CheckClassTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $setUper = new SetUper();
        $setUper->setUp();
    }

    /** @test */
    public function check_worker_is_available_should_return_true_if_worker_is_available()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => 1]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12'
        ]));
        $this->assertTrue(Check::workerIsAvailable($worker, '2020-12-11'));
    }

    /** @test */
    public function check_worker_is_available_should_return_false_if_worker_is_not_available()
    {
        $this->withoutExceptionHandling();
        $worker = factory(Worker::class)->create(['work_place_id' => 1]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12'
        ]));
        $this->assertFalse(Check::workerIsAvailable($worker, '2020-12-12'));
    }

    /** @test */
    public function check_worker_is_available_should_return_false_if_indisposition_time_diffrence_is_less_than_than_months_working_hours()
    {
        $this->withoutExceptionHandling();
        $worker = factory(Worker::class)->create(['work_place_id' => 1]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12',
            'start' => '12:00',
            'end' => '21:30'
        ]));
        $this->assertFalse(Check::workerIsAvailable($worker, '2020-12-12'));
    }

    /** @test */
    public function check_worker_is_available_should_return_true_if_indisposition_time_diffrence_is_more_then_than_months_working_hours()
    {
        $this->withoutExceptionHandling();
        $worker = factory(Worker::class)->create(['work_place_id' => 1]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12',
            'start' => '15:00',
            'end' => '21:30'
        ]));
        $this->assertTrue(Check::workerIsAvailable($worker, '2020-12-12'));
    }
}
