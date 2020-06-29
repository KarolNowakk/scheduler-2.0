<?php

namespace App\Policies;

use App\User;
use App\WorkPlace;
use App\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShiftPolicy
{
    use HandlesAuthorization;

    public function edit(User $user, WorkPlace $workPlace)
    {
        return Permission::where('user_id', $user->id)->where('work_place_id', $workPlace->id)->exists();
    }
}
