<?php 

namespace App\Services;

use App\Worker;
use Illuminate\Support\Carbon;

class ScheduleDay {

    protected $workersIds = [];
    protected $day = null;

    public function __construct(string $day)
    {
        $this->day = $day;
    }

    public function createShift(Worker $worker)
    {

    }

    protected function getShiftStart(Worker $worker)
    {

    }

    protected function getShiftEnd()
    {
        
    }
}