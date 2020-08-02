<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\WorkPlace::class => \App\Policies\AppPolicy::class,
        \App\Shift::class => \App\Policies\AppPolicy::class,
        \App\Worker::class => \App\Policies\AppPolicy::class,
        \App\Accessibility::class => \App\Policies\AppPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
