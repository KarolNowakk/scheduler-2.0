<?php

namespace App\Services;

use App\Worker;
use Illuminate\Support\Carbon;

class WorkerShiftCreator
{
    protected $worker;
    protected $workPlace;
    protected $day;
    protected $shift;

    public function __construct(Worker $worker, string $day, object $shift)
    {
        $this->worker = $worker;
        $this->workPlace = $worker->workPlace;
        $this->day = $day;
        $this->shift = $shift;
    }

    public function create()
    {
        return $this->worker->shifts()->create([
            'day' => $this->day,
            'work_place_id' => $this->workPlace->id,
            'shift_start' => $this->shift->start,
            'shift_end' => $this->shift->end,
            'auto_created' => true,
        ]);
    }
}
