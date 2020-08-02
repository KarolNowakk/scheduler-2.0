<?php

namespace App;

use App\Interfaces\ToUserRelationsInterface;
use App\Traits\ToUserRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\ModelEventObserver;

class Shift extends Model implements ToUserRelationsInterface
{
    use SoftDeletes, ToUserRelations;

    protected $guarded = [];
    
    public function workPlace()
    {
        return $this->belongsTo(WorkPlace::class, 'work_place_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
