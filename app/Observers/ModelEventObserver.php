<?php

namespace App\Observers;

use App\Indisposition;
use Illuminate\Support\Facades\Auth;
use app\Interfaces\ToUserRelationsInterface;

class ModelEventObserver
{
    /**
     * Handle the Model "creating" event.
     *
     * @param  ToUserRelationsInterface  $model
     * @return void
     */
    public function creating(ToUserRelationsInterface $model)
    {
        if (Auth::check()) {
            $model->updated_by = Auth::id();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = 1;
            $model->created_by = 1;
        }
    }

    /**
     * Handle the Model "updating" event.
     *
     * @param  ToUserRelationsInterface  $model
     * @return void
     */
    public function updating(ToUserRelationsInterface $model)
    {
        if (Auth::check()) {
            $model->updated_by = Auth::id();
            $model->created_by = Auth::id();
        } else {
            $model->updated_by = 1;
            $model->created_by = 1;
        }
    }

    /**
     * Handle the Model "deleting" event.
     *
     * @param  ToUserRelationsInterface  $model
     * @return void
     */
    public function deleting(ToUserRelationsInterface $model)
    {
        if ($this->usesSoftDelete($model)) {
            $model->deleted_by = Auth::id();
        }
    }

    /**
     * Checks if Model uses softDelete trait
     *
     * @param  ToUserRelationsInterface  $model
     * @return boolean
     */
    protected function usesSoftDelete(ToUserRelationsInterface $model)
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model));
    }
}
