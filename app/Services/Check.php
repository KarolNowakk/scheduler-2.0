<?php

namespace App\Services;

use App\Worker;
use Illuminate\Support\Carbon;

class Check
{
    public static function workerIsAvailable(Worker $worker, string $day) : bool
    {
        if ($worker->indisposition()->where('day', $day)->first() != null) {
            $indisposition = $worker->indisposition()->where('day', $day)->first();
            $requirements = $worker->workPlace->monthlyRequirements()
                ->where('month', substr($day, 0, 7))
                ->first()->toArray();

            $dayOfTheWeek = config('storage.weekDays')[date('w', strtotime($day))];
            $start = explode(',', $requirements[$dayOfTheWeek])[0];
            $end = explode(',', $requirements[$dayOfTheWeek])[1];
            $startDiffrence = (strtotime($indisposition->start) - strtotime($start)) /60/60;
            $endDiffrence = (strtotime($end) - strtotime($indisposition->end)) /60/60;

            if (($startDiffrence < $requirements['min_working_hours']) and ($endDiffrence < $requirements['min_working_hours'])) {
                return false;
            }
            return true;
        }

        return true;
    }
}
