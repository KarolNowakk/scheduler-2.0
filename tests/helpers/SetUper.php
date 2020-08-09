<?php

namespace Tests\Helpers;

use App\Indisposition;
use App\MonthlyRequirments;
use App\User;
use App\WorkPlace;
use App\Permission;
use App\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class basically seeds databse with all data needed for generating schedule
 */
class SetUper
{
    protected $creator;
    protected $workPlace;
    protected $month;
    protected $workers;
    protected $requirements;

    public function __construct()
    {
        $this->creator = factory(User::class)->create();
        $this->workPlace = factory(WorkPlace::class)->create();
        $this->month = '2020-12';
    }

    protected function createWorker() : Worker
    {
        return factory(Worker::class)->create([
            'work_place_id' => $this->workPlace->id
        ]);
    }

    protected function createInispositionsFor($howMany, $worker = null, $start = '07:30', $end = '21:30') : void
    {
        $worker = is_null($worker) ? $this->createWorker() : $worker;
        
        for ($i = 0; $i < $howMany; $i++) {
            factory(Indisposition::class)->create([
                'worker_id' => $worker->id,
                'day' => $this->month . '-' . random_int(1, 29),
                'start' => $start,
                'end' => $end
            ]);
        }
    }

    protected function createMonthlyRequirements() : void
    {
        $this->requirements = factory(MonthlyRequirments::class)->create([
            'month' => $this->month,
            'work_place_id' => $this->workPlace->id
        ]);
    }

    protected function createMonthlyDataForWorker() : void
    {
        $this->workers->each(function ($worker) {
            $worker->monthlyData()->create([
                'hours_to_be_worked' => random_int(40, 200),
                'month' => $this->month
            ]);
        });
    }

    public function setUp() : void
    {
        $this->workers = collect();
        for ($i = 0; $i < 11; $i++) {
            $this->workers->push($this->createWorker());
        }

        $this->workers->each(function ($worker) {
            $this->createInispositionsFor(random_int(2, 6), $worker);
        });

        $this->createMonthlyRequirements();
        $this->createMonthlyDataForWorker();
    }

    // --------------------------------------------------------
    public function getCreator() : User
    {
        return $this->creator;
    }

    public function getWorkPlace() : WorkPlace
    {
        return $this->workPlace;
    }

    public function getMonth() : string
    {
        return $this->month;
    }

    public function getWorker() : Worker
    {
        return $this->workers->random();
    }

    public function getWorkers() : \Illuminate\Support\Collection
    {
        return $this->workers;
    }

    public function getRequirements() : MonthlyRequirments
    {
        return $this->requirements;
    }
}
