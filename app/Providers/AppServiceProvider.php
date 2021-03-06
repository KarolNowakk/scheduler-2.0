<?php

namespace App\Providers;

use App\Indisposition;
use App\MonthlyRequirments;
use App\Observers\ModelEventObserver;
use App\Permission;
use App\Shift;
use App\Worker;
use App\WorkPlace;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        WorkPlace::observe(ModelEventObserver::class);
        Worker::observe(ModelEventObserver::class);
        Permission::observe(ModelEventObserver::class);
        Indisposition::observe(ModelEventObserver::class);
        Shift::observe(ModelEventObserver::class);
        MonthlyRequirments::observe(ModelEventObserver::class);
    }
}
