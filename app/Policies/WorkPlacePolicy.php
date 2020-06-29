<?php

namespace App\Policies;

use App\Permission;
use App\User;
use App\WorkPlace;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class WorkPlacePolicy
{
    use HandlesAuthorization;

    public function delete(User $user, WorkPlace $workPlace)
    {
        return $workPlace->createdBy->id == $user->id;
    }

    public function edit(User $user, WorkPlace $workPlace)
    {
        if ($workPlace->createdBy->id == $user->id) {
            return true;
        }

        return Permission::where('user_id', $user->id)->where('work_place_id', $workPlace->id)->exists();
    }
}
