<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Driver;
use App\Models\Staff;
use App\Models\User;
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
            'visitor' => Visitor::class,
            'staff' => Staff::class,
            'driver' => Driver::class,
            'user' => User::class,
            'admin' => Admin::class,
        ]);
    }
}
