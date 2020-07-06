<?php

namespace App\Providers;

use App\Observers\ModelEventObserver;
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
        // WorkPlace::observe(ModelEventObserver::class);
    }
}
