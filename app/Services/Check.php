<?php

namespace App\Services;

use App\Worker;
use DateTime;

class Check
{
    public static function workerAllreadyReachedHoursToBeWorkedLimit(Worker $worker, string $month) : bool
    {
        $data = $worker->monthlyData()->where('month', $month)->first();
        return $data->hours_worked > $data->hours_to_be_worked + 10;
    }
    
    public static function allWorkersHasAllreadyBeenChecked(int $workersCount, array $workersIds) : bool
    {
        return $workersCount === count($workersIds);
    }

    public static function workerIsAllreadyChecked(Worker $worker, array $workersIds) : bool
    {
        return in_array($worker->id, $workersIds);
    }

    public static function workerIsNotAvailable(Worker $worker, string $day, object $shift) : bool
    {
        if ($worker->indisposition()->where('day', $day)->first() === null) {
            return false;
        }
        $indisposition = $worker->indisposition()->where('day', $day)->first();
        $requirements = $worker->workPlace->monthlyRequirements()
            ->where('month', substr($day, 0, 7))
            ->first()->toArray();

        $startDiffrence = (strtotime($indisposition->start) - strtotime($shift->start)) /60/60;
        $endDiffrence = (strtotime($shift->end) - strtotime($indisposition->end)) /60/60;

        if (($startDiffrence < $requirements['min_working_hours']) and ($endDiffrence < $requirements['min_working_hours'])) {
            return true;
        }
        return false;
    }

    public static function workerAllreadyWorksThisDay(Worker $worker, array $workersIds) : bool
    {
        return in_array($worker->id, $workersIds);
    }

    public static function workerWorksToManyDaysInRow(Worker $worker, string $month) : bool
    {
        $workingDays = $worker->shifts()->where('day', '>=', date('Y-m' . '-01'))->get()->pluck('day');
        $daysInARow = 1;
        for ($i = 0; $i < $workingDays->count();$i++) {
            if ($i >= $workingDays->count() - 1) {
                break;
            }
            $firstInRow = new DateTime($workingDays[$i]);
            $nextWorkigDay = new DateTime($workingDays[$i + 1]);
            $tommorow = $firstInRow->modify('+1 day');
            $daysInARow = $tommorow == $nextWorkigDay ? $daysInARow + 1 : 1;
        }

        return $daysInARow > $worker->workPlace->monthlyRequirements()->where('month', $month)->first()->max_days_in_row;
    }
}
