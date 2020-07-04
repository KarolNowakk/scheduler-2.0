<?php

namespace App\Policies;

use App\Permission;
use App\User;
use App\WorkPlace;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppPolicy
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
        $trueodfalse = Permission::where('user_id', $user->id)->where('work_place_id', $workPlace->id)->exists();
        return Permission::where('user_id', $user->id)->where('work_place_id', $workPlace->id)->exists();
    }

    public function accessToView(User $user, WorkPlace $workPlace)
    {
        foreach ($user->worksAs as $worker) {
            if ($worker->workPlace->id == $workPlace->id) {
                // dd($worker->workPlace->id, $workPlace->id);
                return true;
            }
        }
        return false;
    }
}
