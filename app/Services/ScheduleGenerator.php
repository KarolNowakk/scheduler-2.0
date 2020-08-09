<?php

namespace App\Services;

use App\WorkPlace;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class ScheduleGenerator
{
    protected $workPlace;
    protected $month;

    public function __construct(WorkPlace $workPlace, string $month)
    {
        $this->workPlace = $workPlace;
        $this->month = $month;
    }

    public function generate()
    {
        $month = Carbon::create($this->month);
        $period = CarbonPeriod::create($month->startOfMonth()->toDateString(), $month->endOfMonth()->toDateString());
        $period->setDateInterval(CarbonInterval::make(1, 'days'));
        
        foreach ($period as $value) {
            $dayScheduler = new ScheduleDay($this->workPlace, $value->toDateString(), $this->month);
            $dayScheduler->schedule();
        }
    }
}
