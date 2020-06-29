<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPlace extends Model
{
    use SoftDeletes;
    
    protected $guarded= [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workers()
    {
        return $this->hasMany(Worker::class, 'work_place_id');
    }

    public function grantedPermissions()
    {
        return $this->hasMany(Permission::class, 'work_place_id');
    }
}
