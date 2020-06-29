<?php

use App\User;
use App\WorkPlace;
use App\Permission;

function setUpPermissionForUser()
{
    $user = factory(User::class)->create();
    $creator_of_work_place = factory(User::class)->create();
    $workPlace = factory(WorkPlace::class)->create(['created_by' => $creator_of_work_place->id]);
    $permission = factory(Permission::class)->create([
        'user_id' =>  $user->id,
        'work_place_id' => $workPlace->id,
        'type' => 'can_edit',
    ]);

    return [
        'user' => $user,
        'creator_of_work_place' => $creator_of_work_place,
        'workPlace' => $workPlace,
        'permission' => $permission,
    ];
}
