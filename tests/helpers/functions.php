<?php

use App\User;
use App\WorkPlace;
use App\Permission;
use App\Worker;
use App\Shift;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;

function setUpPermissionForUser($user = null, $type = 'can_edit')
{
    if (! $user) {
        $user = factory(User::class)->create();
    }
    signIn($user);
    $creator_of_work_place = factory(User::class)->create();
    $workPlace = factory(WorkPlace::class)->create(['created_by' => $creator_of_work_place->id]);
    $permission = factory(Permission::class)->create([
        'user_id' =>  $user->id,
        'work_place_id' => $workPlace->id,
        'type' => $type,
    ]);

    return [
        'user' => $user,
        'creator_of_work_place' => $creator_of_work_place,
        'workPlace' => $workPlace,
        'permission' => $permission,
    ];
}



function signIn($user = null)
{
    if (!$user) {
        $user = factory(User::class)->create();
    }

    Passport::actingAs($user);

    return $user;
}

function signOut(User $user = null)
{
    if (! $user) {
        if (Auth::check()) {
            Auth::user()->tokens->each(function ($token) {
                $token->delete();
            });
            return;
        }
        return;
    }

    $user->tokens->each(function ($token) {
        $token->delete();
    });
}

function getShiftAndCreateWorker($attributes = [])
{
    $worker = factory(Worker::class)->create(['user_id' => $attributes['user']->id, 'work_place_id' => $attributes['workPlace']->id]);
    $shift = factory(Shift::class)->raw(['worker_id' => $worker->id, 'work_place_id' => $attributes['workPlace']->id]);

    return [
        'worker' => $worker,
        'shift' => $shift,
    ];
}

function createShiftAndWorker($attributes = [])
{
    if (!empty($attributes)) {
        $worker = factory(Worker::class)->create(['user_id' => $attributes['user']->id, 'work_place_id' => $attributes['workPlace']->id]);
        $shift = factory(Shift::class)->create(['worker_id' => $worker->id, 'work_place_id' => $attributes['workPlace']->id]);
    } else {
        $worker = factory(Worker::class)->create();
        $shift = factory(Shift::class)->create(['worker_id' => $worker->id]);
    }

    return [
        'worker' => $worker,
        'shift' => $shift,
    ];
}

function createShiftWorkerAndWorkPlace()
{
    $user = factory(User::class)->create();
    $workPlace = factory(WorkPlace::class)->create();
    $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id]);
    $shift = factory(Shift::class)->create(['worker_id' => $worker->id, 'work_place_id' => $workPlace->id]);
    return [
        'worker' => $worker,
        'shift' => $shift,
        'workPlace' => $workPlace,
    ];
}

function createWorkerAndWorkPlace()
{
    $user = factory(User::class)->create();
    $workPlace = factory(WorkPlace::class)->create();
    $worker = factory(Worker::class)->create(['work_place_id' => $workPlace->id]);

    return [
        'worker' => $worker,
        'workPlace' => $workPlace,
    ];
}
