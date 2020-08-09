<?php

namespace App;

use App\Interfaces\ToUserRelationsInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\ModelEventObserver;
use App\Traits\ToUserRelations;

class Worker extends Model implements ToUserRelationsInterface
{
    use SoftDeletes, ToUserRelations;

    protected $guarded = [];

    public function workPlace()
    {
        return $this->belongsTo(WorkPlace::class, 'work_place_id');
    }

    public function belongsToUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class, 'worker_id');
    }

    public function indisposition()
    {
        return $this->hasMany(Indisposition::class, 'worker_id');
    }

    public function monthlyData()
    {
        return $this->hasMany(MonthlyWorkerData::class, 'worker_id');
    }
}
