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

    public static function boot()
    {
        parent::boot();

        self::observe(new ModelEventObserver);
    }

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

    public function availability()
    {
        return $this->hasMany(Availability::class, 'worker_id');
    }
}
