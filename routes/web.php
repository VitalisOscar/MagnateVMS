<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')
// ->middleware('auth:admin')
->group(function () {

    Route::view('login', 'admin.auth.login')->withoutMiddleware('auth:admin')->name('admin.login');
    Route::post('login', \App\Http\Controllers\Admin\Auth\LoginController::class)->withoutMiddleware('auth:admin')->name('admin.login');

    Route::view('dashboard', 'admin.dashboard')->name('admin.dashboard');

    // Users
    Route::prefix('users')->group(function () {
        Route::get('', \App\Http\Controllers\Admin\Users\GetUsersController::class)->name('admin.users');
        Route::view('add', 'admin.users.add')->name('admin.users.add');
        Route::post('add', \App\Http\Controllers\Admin\Users\AddUserController::class)->name('admin.users.add');
        Route::get('logins', \App\Http\Controllers\Admin\Users\GetUsersController::class)->name('admin.users.logins');

        Route::get('{username}', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'get'])->name('admin.users.single');
        Route::post('{username}/update', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'update'])->name('admin.users.update');
        Route::post('{username}/delete', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'delete'])->name('admin.users.delete');
    });


    // Sites
    Route::prefix('sites')->group(function () {
        Route::get('', \App\Http\Controllers\Admin\Sites\GetSitesController::class)->name('admin.sites');
        Route::view('add', 'admin.sites.add')->name('admin.sites.add');
        Route::post('add', \App\Http\Controllers\Admin\Sites\AddSiteController::class)->name('admin.sites.add');

        Route::get('{site_id}', [\App\Http\Controllers\Admin\Sites\SingleSiteController::class, 'get'])->name('admin.sites.single');
        Route::post('{site_id}/update', [\App\Http\Controllers\Admin\Sites\SingleSiteController::class, 'update'])->name('admin.sites.update');
        // Route::post('{site_id}/delete', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'delete'])->name('admin.users.delete');

        // Route::get('{site_id}/companies', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'get'])->name('admin.users.single');
        // Route::get('{site_id}/companies/{company_id}/delete', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'get'])->name('admin.users.single');
        Route::get('{site_id}/companies/add', [\App\Http\Controllers\Admin\Sites\SingleSiteController::class, 'getSiteForAdd'])->name('admin.sites.company.add');
        Route::post('{site_id}/companies/add', [\App\Http\Controllers\Admin\Sites\SingleSiteController::class, 'addCompany'])->name('admin.sites.company.add');
        Route::get('{site_id}/companies/{company_id}', [\App\Http\Controllers\Admin\Sites\SingleCompanyController::class, 'get'])->name('admin.sites.company');

        // staff
        Route::get('{site_id}/companies/{company_id}/staff/add', [\App\Http\Controllers\Admin\Staff\StaffController::class, 'getForAdd'])->name('admin.sites.staff.add');
        Route::post('{site_id}/companies/{company_id}/staff/add', [\App\Http\Controllers\Admin\Staff\StaffController::class, 'add'])->name('admin.sites.staff.add');
        Route::get('{site_id}/companies/{company_id}/staff/{staff_id}', [\App\Http\Controllers\Admin\Staff\SingleStaffController::class, 'get'])->name('admin.sites.staff');
        Route::post('{site_id}/companies/{company_id}/staff/{staff_id}/update', [\App\Http\Controllers\Admin\Staff\SingleStaffController::class, 'update'])->name('admin.sites.staff.update');

    });

    // Vehicles
    Route::prefix('vehicles')->group(function () {
        Route::get('', \App\Http\Controllers\Admin\Vehicles\GetVehiclesController::class)->name('admin.vehicles');
        Route::view('add', 'admin.vehicles.add')->name('admin.vehicles.add');
        Route::post('add', \App\Http\Controllers\Admin\Vehicles\AddVehicleController::class)->name('admin.vehicles.add');
        Route::get('{vehicle_id}', [\App\Http\Controllers\Admin\Vehicles\SingleVehicleController::class, 'get'])->name('admin.vehicles.single');
    });

    // Drivers
    Route::prefix('drivers')->group(function () {
        Route::get('', \App\Http\Controllers\Admin\Drivers\GetDriversController::class)->name('admin.vehicles.drivers');
        Route::view('add', 'admin.drivers.add')->name('admin.vehicles.drivers.add');
        Route::post('add', \App\Http\Controllers\Admin\Drivers\AddDriverController::class)->name('admin.vehicles.drivers.add');
        Route::get('{driver_id}', [\App\Http\Controllers\Admin\Drivers\SingleDriverController::class, 'get'])->name('admin.vehicles.drivers.single');
        Route::post('{driver_id}/update', [\App\Http\Controllers\Admin\Drivers\SingleDriverController::class, 'update'])->name('admin.vehicles.drivers.update');
    });

    // History
    Route::prefix('history')->group(function () {
        Route::get('logins', \App\Http\Controllers\Admin\History\LoginHistoryController::class)->name('admin.history.logins');
    });


    // Staff


    // Visitors
    Route::prefix('visitors')->group(function () {
        Route::get('', \App\Http\Controllers\Admin\Visitors\GetVisitorsController::class)->name('admin.visitors');
        Route::get('visits', \App\Http\Controllers\Admin\Visitors\VisitsController::class)->name('admin.visitors.visits');
        Route::get('{visitor_id}', [\App\Http\Controllers\Admin\Visitors\SingleVisitorController::class, 'get'])->name('admin.visitors.single');
    });

    // Exports
    Route::prefix('exports')->group(function () {
        Route::get('visits', [\App\Http\Controllers\Admin\Visitors\VisitsController::class, 'export'])->name('admin.exports.visits');
    });

    // History


    // Stats


    // Admin

});
