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

    protected $setUper;

    public function setUp() : void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->setUper = new SetUper();
        $this->setUper->setUp();
    }

    /** @test */
    public function check_worker_is_available_should_return_false_if_worker_is_available()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12'
        ]));
        $req = factory(MonthlyRequirments::class)->raw(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $shift = json_decode($req['friday'])[0];
        $this->assertFalse(Check::workerIsNotAvailable($worker, '2020-12-11', $shift));
    }

    /** @test */
    public function check_worker_is_available_should_return_true_if_worker_is_not_available()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12'
        ]));
        $req = factory(MonthlyRequirments::class)->raw(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $shift = json_decode($req['friday'])[0];
        $this->assertTrue(Check::workerIsNotAvailable($worker, '2020-12-12', $shift));
    }

    /** @test */
    public function check_worker_is_available_should_return_true_if_indisposition_time_diffrence_is_less_than_than_months_working_hours()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12',
            'start' => '12:00',
            'end' => '21:30'
        ]));
        $req = factory(MonthlyRequirments::class)->raw(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $shift = json_decode($req['friday'])[0];
        $this->assertTrue(Check::workerIsNotAvailable($worker, '2020-12-12', $shift));
    }

    /** @test */
    public function check_worker_is_available_should_return_false_if_indisposition_time_diffrence_is_more_then_than_months_working_hours()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $indisposition = $worker->indisposition()->create(factory(Indisposition::class)->raw([
            'day' => '2020-12-12',
            'start' => '15:00',
            'end' => '21:30'
        ]));
        $req = factory(MonthlyRequirments::class)->raw(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        $shift = json_decode($req['friday'])[0];
        $this->assertFalse(Check::workerIsNotAvailable($worker, '2020-12-12', $shift));
    }

    /** @test */
    public function check_if_worker_works_to_many_days_in_row_returns_true_if_worker_works_more_than_specified_in_monthly_requirements()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        for ($i = 1; $i <= 5; $i++) {
            $worker->shifts()->create(factory(Shift::class)->raw([
                'work_place_id' => $this->setUper->getWorkPlace()->id,
                'day' => $this->setUper->getMonth() . '-0' . $i
            ]));
        }

        $this->assertTrue(Check::workerWorksToManyDaysInRow($worker, $this->setUper->getMonth()));
    }

    /** @test */
    public function check_if_worker_works_to_many_days_in_row_returns_false_if_worker_works_less_than_specified_in_monthly_requirements()
    {
        $worker = factory(Worker::class)->create(['work_place_id' => $this->setUper->getWorkPlace()->id]);
        for ($i = 1; $i <= 3; $i++) {
            $worker->shifts()->create(factory(Shift::class)->raw([
                'work_place_id' => $this->setUper->getWorkPlace()->id,
                'day' => $this->setUper->getMonth() . '-0' . $i
            ]));
        }

        $this->assertFalse(Check::workerWorksToManyDaysInRow($worker, $this->setUper->getMonth()));
    }
}
