<?php

namespace App\Observers;

use App\WorkPlace;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ToUserRelationsInterface;

class ModelEventObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  ToUserRelationsInterface  $model
     * @return void
     */
    public function created(ToUserRelationsInterface $model)
    {
        $model->created_by = Auth::id();
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  ToUserRelationsInterface  $model
     * @return void
     */
    public function updated(ToUserRelationsInterface $model)
    {
        $model->updated_by = Auth::id();
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  ToUserRelationsInterface  $model
     * @return void
     */
    public function deleted(ToUserRelationsInterface $model)
    {
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
            $model->deleted_by = Auth::id();
        }
    }
}
