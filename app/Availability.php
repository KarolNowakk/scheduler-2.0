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

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}
