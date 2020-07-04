<?php

namespace App\Observers;

use App\WorkPlace;
use Illuminate\Support\Facades\Auth;

class ModelEventObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(WorkPlace $workPlace)
    {
        $workPlace->created_by = Auth::id();
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(WorkPlace $workPlace)
    {
        $workPlace->updated_by = Auth::id();
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(WorkPlace $workPlace)
    {
        $workPlace->deleted_by = Auth::id();
    }
}
