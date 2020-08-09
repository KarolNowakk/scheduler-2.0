<?php

namespace App\Services;

use App\Worker;
use App\WorkPlace;
use App\Shift;
use Illuminate\Support\Carbon;

class ScheduleDay
{
    protected $workPlace;
    protected $day;
    protected $month;
    protected $requirements;

    protected $workersCount;
    protected $allreadyWorkingWorkersIds = [];
    protected $allreadyCheckedWorkersIds = [];

    public function __construct(WorkPlace $workPlace, string $day, string $month)
    {
        $this->workPlace = $workPlace;
        $this->day = $day;
        $this->month = $month;
        $this->requirements = $this->workPlace->monthlyRequirements()
            ->where('month', $this->month)
            ->first()
            ->toArray();
        $this->workersCount = $workPlace->workers->count();
    }

    public function schedule()
    {
        $dayName = config('storage.weekDays')[date('w', strtotime($this->day))];
        $shifts = json_decode($this->requirements[$dayName]);

        foreach ($shifts as $shift) {
            for ($i=0; $i < $shift->min_workers_on_shift; $i++) {
                $this->createShift($shift);
            }
        }
    }

    public function createShift(object $shift)
    {
        while (true) {
            $worker = $this->workPlace->workers->random();

            if (Check::allWorkersHasAllreadyBeenChecked($this->workersCount, $this->allreadyCheckedWorkersIds)) {
                // TODO: create message model
                break;
            }
            if (Check::workerIsAllreadyChecked($worker, $this->allreadyCheckedWorkersIds)) {
                continue;
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
            if (Check::workerAllreadyReachedHoursToBeWorkedLimit($worker, $this->month)) {
                $this->markChecked($worker->id);
                continue;
            }

            $shiftCreator = new WorkerShiftCreator($worker, $this->day, $shift);
            $shift = $shiftCreator->create();
            
            $this->handelShiftCreated($worker, $shift);
            break;
        }
    }

    public function markChecked(int $workerId)
    {
        if (! in_array($workerId, $this->allreadyCheckedWorkersIds)) {
            array_push($this->allreadyCheckedWorkersIds, $workerId);
        }
    }

    public function handelShiftCreated(Worker $worker, Shift $shift)
    {
        array_push($this->allreadyWorkingWorkersIds, $worker->id);

        $workerMonthlyData = $worker->monthlyData()->where('month', $this->month)->first();
        $hoursOnShift = (strtotime($shift->shift_end) - strtotime($shift->shift_start)) / 60 / 60 ;
        $workerMonthlyData->hours_worked +=  $hoursOnShift;
        $workerMonthlyData->save();
    }
}
