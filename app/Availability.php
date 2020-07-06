<?php

namespace App;

use App\Interfaces\ToUserRelationsInterface;
use App\Traits\ToUserRelations;
use Illuminate\Database\Eloquent\Model;
use App\Observers\ModelEventObserver;

class Availability extends Model implements ToUserRelationsInterface
{
    use ToUserRelations;

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        self::observe(new ModelEventObserver);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
