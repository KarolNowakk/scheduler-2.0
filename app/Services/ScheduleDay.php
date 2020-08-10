<?php

namespace App\Services;

use App\SchedulerMessage;
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

    public function schedule() : void
    {
        $dayName = config('storage.weekDays')[date('w', strtotime($this->day))];
        $shifts = json_decode($this->requirements[$dayName]);

        foreach ($shifts as $shift) {
            for ($i=0; $i < $shift->min_workers_on_shift; $i++) {
                if (! $this->createShift($shift)) {
                    break;
                }
            }
        }
    }

    protected function createShift(object $shift) : bool
    {
        while (true) {
            $worker = $this->workPlace->workers->random();

            if (Check::allWorkersHasAllreadyBeenChecked($this->workersCount, $this->allreadyCheckedWorkersIds)) {
                $this->message('Not all shifts are filled at ' . $this->day . '.');
                return false;
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

        return true;
    }

    protected function markChecked(int $workerId) : void
    {
        if (! in_array($workerId, $this->allreadyCheckedWorkersIds)) {
            array_push($this->allreadyCheckedWorkersIds, $workerId);
        }
    }

    protected function handelShiftCreated(Worker $worker, Shift $shift) : void
    {
        array_push($this->allreadyWorkingWorkersIds, $worker->id);

        $workerMonthlyData = $worker->monthlyData()->where('month', $this->month)->first();
        $hoursOnShift = (strtotime($shift->shift_end) - strtotime($shift->shift_start)) / 60 / 60 ;
        $workerMonthlyData->hours_worked +=  $hoursOnShift;
        $workerMonthlyData->save();
    }

    protected function message(string $message) : void
    {
        SchedulerMessage::create([
            'work_place_id' => $this->workPlace->id,
            'month' => $this->month,
            'message' => $message
        ]);
    }
}
