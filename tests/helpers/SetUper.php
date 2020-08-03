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

    public function __construct()
    {
        $this->creator = factory(User::class)->create();
        $this->workPlace = factory(WorkPlace::class)->create();
        $this->month = '2020-12';
    }

    protected function getWorker() : Worker
    {
        return factory(Worker::class)->create([
            'work_place_id' => $this->workPlace->id
        ]);
    }

    protected function createInispositionsFor($howMany, $worker = null, $start = '07:30', $end = '21:30') : void
    {
        $worker = is_null($worker) ? $this->getWorker() : $worker;
        
        for ($i = 0; $i < $howMany; $i++) {
            factory(Indisposition::class)->create([
                'worker_id' => $worker->id,
                'month' => $this->month,
                'start' => $start,
                'end' => $end
            ]);
        }
    }

    protected function createMonthlyRequirements()
    {
        factory(MonthlyRequirments::class)->create([
            'month' => $this->month,
            'work_place_id' => $this->workPlace->id
        ]);
    }

    public function setUp()
    {
        $workers = collect();
        for ($i = 0; $i < 11; $i++) {
            $workers->push($this->getWorker());
        }

        $workers->each(function ($worker) {
            $this->createInispositionsFor(random_int(2, 6), $worker);
        });

        $this->createMonthlyRequirements();
    }
}
