<?php

namespace App;

use App\Interfaces\ToUserRelationsInterface;
use App\Traits\ToUserRelations;
use Illuminate\Database\Eloquent\Model;

class MonthlyRequirments extends Model implements ToUserRelationsInterface
{
    use ToUserRelations;

    protected $guarded = ['id'];
}
