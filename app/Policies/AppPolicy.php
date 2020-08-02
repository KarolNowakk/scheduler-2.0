<?php

namespace App\Policies;

use App\Permission;
use App\Worker;
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
        return $workPlace->createdBy->is($user);
    }

    public function grantPermission(User $user, WorkPlace $workPlace)
    {
        return $workPlace->createdBy->is($user);
    }

    public function edit(User $user, WorkPlace $workPlace)
    {
        if ($workPlace->createdBy->is($user)) {
            return true;
        }

        return Permission::where('type', 'edit')
            ->where('user_id', $user->id)
            ->where('work_place_id', $workPlace->id)
            ->exists();
    }

    public function accessToView(User $user, WorkPlace $workPlace)
    {
        foreach ($user->worksAs as $worker) {
            if ($worker->workPlace->is($workPlace)) {
                return true;
            }
        }
        return false;
    }

    public function editAvailability(User $user, Worker $worker)
    {
        if ((isset($worker->belongsToUser)) and ($worker->belongsToUser->is($user))) {
            return true;
        }
        
        return Permission::where('type', 'edit')
            ->where('user_id', $user->id)
            ->where('work_place_id', $worker->workPlace->id)
            ->exists();
    }
}
