<?php

namespace App;

use App\Interfaces\ToUserRelationsInterface;
use App\Traits\ToUserRelations;
use Illuminate\Database\Eloquent\Model;
use App\Observers\ModelEventObserver;

class Permission extends Model implements ToUserRelationsInterface
{
    use ToUserRelations;

    protected $guarded = ['id'];
    
    public function workPlace()
    {
        return $this->belongsTo(WorkPlace::class, 'work_place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
