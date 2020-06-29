<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function workPlace()
    {
        return $this->belongsTo(WorkPlace::class, 'work_place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
