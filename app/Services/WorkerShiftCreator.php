<?php 

namespace App\Services;

use App\Worker;
use Illuminate\Support\Carbon;

class WorkerShiftCreator {

    protected $worker = null;
    protected $day = null;

    public function __construct(Worker $worker,string $day)
    {
        $this->worker = $worker;
        $this->day = $day;
    }

    public function insert()
    {
        $this->worker->shifts()->create([
            'day' => $this->day,
            'shift_start' => $this->getShiftStart(),
            'shift_end' => $this->getShiftEnd(),
        ]);
    }

    protected function getShiftStart()
    {
        
    }

    protected function getShiftEnd()
    {
        
    }
}