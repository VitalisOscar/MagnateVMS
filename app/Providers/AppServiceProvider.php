<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Driver;
use App\Models\Staff;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::morphMap([
            'Visitor' => Visitor::class,
            'Vehicle' => Vehicle::class,
            'Staff' => Staff::class,
            'Driver' => Driver::class,
            'User' => User::class,
            'user' => User::class,
            'Admin' => Admin::class,
        ]);
    }
}
