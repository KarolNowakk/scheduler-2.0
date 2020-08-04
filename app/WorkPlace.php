<?php

namespace App;

use App\Interfaces\ToUserRelationsInterface;
use App\Observers\ModelEventObserver;
use App\Traits\ToUserRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPlace extends Model implements ToUserRelationsInterface
{
    use SoftDeletes, ToUserRelations;
    
    protected $guarded= ['id'];
    
    public function workers()
    {
        return $this->hasMany(Worker::class, 'work_place_id');
    }

    public function grantedPermissions()
    {
        return $this->hasMany(Permission::class, 'work_place_id');
    }

    public function monthlyRequirements()
    {
        return $this->hasMany(MonthlyRequirments::class, 'work_place_id');
    }
}
