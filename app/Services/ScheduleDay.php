<?php

namespace App\Services;

use App\Worker;
use App\WorkPlace;
use Illuminate\Support\Carbon;

class ScheduleDay
{
    protected $allreadyWorkingWorkersIds = [];
    protected $allreadyCheckedWorkersIds = [];
    protected $workersCount = null;
    protected $day = null;
    protected $workPlace;

    public function __construct(WorkPlace $workPlace, string $day, string $month)
    {
        $this->workPlace = $workPlace;
        $this->workersCount = $workPlace->workers->count();
        $this->day = $day;
        $this->month = $month;
    }

    public function schedule()
    {
        $this->shifts->each(function ($shift) {
            $this->createShift($shift);
        });
    }

    public function createShift(object $shift)
    {
        while (true) {
            $worker = $this->workPlace->workers->random();

            if (Check::workerIsAllreadyChecked($this->workersCount, $this->allreadyCheckedWorkersIds)) {
                // TODO: create message model
                break;
            }
            if (Check::workerIsNotAvailable($worker, $this->day, $shift)) {
                $this->markChecked($worker->id);
                continue;
            }
            if (Check::workerAllreadyWorksThisDay($worker, $this->allreadyWorkingWorkersIds)) {
                $this->markChecked($worker->id);
                continue;
            }
            if (Check::workerWorksToManyDaysInRow($worker, $this->month)) {
                $this->markChecked($worker->id);
                continue;
            }

            $shiftCreator = new WorkerShiftCreator($worker, $this->day, $shift);
            $shiftCreator->create();
            
            array_push($this->allreadyWorkingWorkersIds, $worker->id);
            break;
        }
    }

    public function markChecked(int $workerId)
    {
        if (! in_array($workerId, $this->allreadyCheckedWorkersIds)) {
            array_push($this->allreadyCheckedWorkersIds, $workerId);
        }
    }
}
