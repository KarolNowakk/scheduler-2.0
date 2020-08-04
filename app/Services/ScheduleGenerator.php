<?php 

namespace App\Services;

use Illuminate\Support\Carbon;

class ScheduleGenerator {

    protected $month = null;
    protected $daysInMonth = null;

    public function __construct(string $month)
    {   
        $this->month = Carbon::create($month);
        $this->daysInMonth = $this->month->daysInMonth;
    }

    public function generateDay()
    {
        
    }
}