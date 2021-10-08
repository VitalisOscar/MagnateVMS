<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')
->middleware('auth:admin')
->name('admin.')
->group(function () {

    Route::get('logout', function(){
        auth('admin')->logout();
        return redirect()->route('admin.login');
    })->name('logout');

    Route::view('login', 'admin.auth.login')->withoutMiddleware('auth:admin')->name('login');
    Route::post('login', \App\Http\Controllers\Auth\AdminLoginController::class)->withoutMiddleware('auth:admin')->name('login');

    Route::view('password', 'admin.auth.change-password')->name('change_password');
    Route::post('password', [\App\Http\Controllers\Auth\AdminPasswordController::class, 'change'])->name('change_password');

    Route::get('', \App\Http\Controllers\AdminDashboardController::class)->name('dashboard');

    // Sites
    Route::prefix('sites')->group(function () {
        Route::get('', \App\Http\Controllers\Data\Sites\GetSitesController::class)->name('sites');
        Route::view('add', 'sites.add')->name('sites.add');
        Route::post('add', \App\Http\Controllers\Data\Sites\AddSiteController::class)->name('sites.add');

        Route::get('{site_id}', [\App\Http\Controllers\Data\Sites\SingleSiteController::class, 'get'])->name('sites.single');
        Route::post('{site_id}/update', [\App\Http\Controllers\Data\Sites\SingleSiteController::class, 'update'])->name('sites.update');
        // Route::post('{site_id}/delete', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'delete'])->name('admin.users.delete');

        // Route::get('{site_id}/companies', [\App\Http\Controllers\Admin\Users\SingleUserController::class, 'get'])->name('admin.users.single');
        Route::get('{site_id}/companies/add', \App\Http\Controllers\Data\Sites\AddCompanyController::class)->name('sites.company.add');
        Route::post('{site_id}/companies/add', \App\Http\Controllers\Data\Sites\AddCompanyController::class)->name('sites.company.add');
        Route::get('{site_id}/companies/{company_id}', [\App\Http\Controllers\Data\Sites\SingleCompanyController::class, 'get'])->name('sites.company');
        Route::post('{site_id}/companies/delete', [\App\Http\Controllers\Data\Sites\SingleCompanyController::class, 'delete'])->name('sites.company.delete');

        // staff
        Route::get('{site_id}/companies/{company_id}/staff/add', \App\Http\Controllers\Data\Staff\AddStaffController::class)->name('sites.staff.add');
        Route::post('{site_id}/companies/{company_id}/staff/add', \App\Http\Controllers\Data\Staff\AddStaffController::class)->name('sites.staff.add');
        Route::get('{site_id}/companies/{company_id}/staff/{staff_id}', [\App\Http\Controllers\Data\Staff\SingleStaffController::class, 'get'])->name('sites.staff');
        Route::post('{site_id}/companies/{company_id}/staff/{staff_id}/update', [\App\Http\Controllers\Data\Staff\SingleStaffController::class, 'update'])->name('sites.staff.update');
        Route::post('{site_id}/companies/{company_id}/staff/{staff_id}/delete', [\App\Http\Controllers\Data\Staff\SingleStaffController::class, 'delete'])->name('sites.staff.delete');

    });

    // Vehicles
    Route::prefix('vehicles')->group(function () {
        Route::get('company', [\App\Http\Controllers\Data\Vehicles\GetVehiclesController::class, 'company'])->name('vehicles');
        Route::get('other', [\App\Http\Controllers\Data\Vehicles\GetVehiclesController::class, 'other'])->name('vehicles.other');
        Route::view('add', 'admin.vehicles.add')->name('vehicles.add');
        Route::post('add', \App\Http\Controllers\Data\Vehicles\AddVehicleController::class)->name('vehicles.add');
        Route::get('{vehicle_id}', [\App\Http\Controllers\Data\Vehicles\SingleVehicleController::class, 'get'])->name('vehicles.single');
    });

    // Drivers
    Route::prefix('drivers')->group(function () {
        Route::get('', \App\Http\Controllers\Data\Drivers\GetDriversController::class)->name('vehicles.drivers');
        Route::view('add', 'admin.drivers.add')->name('admin.vehicles.drivers.add');
        Route::post('add', \App\Http\Controllers\Data\Drivers\AddDriverController::class)->name('vehicles.drivers.add');
        Route::get('{driver_id}', [\App\Http\Controllers\Data\Drivers\SingleDriverController::class, 'get'])->name('vehicles.drivers.single');
        Route::post('{driver_id}/update', [\App\Http\Controllers\Data\Drivers\SingleDriverController::class, 'update'])->name('vehicles.drivers.update');
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('', \App\Http\Controllers\Data\Users\GetUsersController::class)->name('users');
        Route::view('add', 'admin.users.add')->name('admin.users.add');
        Route::post('add', \App\Http\Controllers\Data\Users\AddUserController::class)->name('users.add');
        Route::get('logins', \App\Http\Controllers\Data\Users\GetUsersController::class)->name('users.logins');

        Route::get('{username}', [\App\Http\Controllers\Data\Users\SingleUserController::class, 'get'])->name('users.single');
        Route::post('{username}/update', [\App\Http\Controllers\Data\Users\SingleUserController::class, 'update'])->name('users.update');
        Route::post('{username}/delete', [\App\Http\Controllers\Data\Users\SingleUserController::class, 'delete'])->name('users.delete');
    });

    // History
    Route::prefix('history')->group(function () {
        Route::get('logins', \App\Http\Controllers\Data\History\LoginHistoryController::class)->name('history.logins');
    });

    // Activity
    Route::prefix('activity')->group(function () {
        Route::get('staff', \App\Http\Controllers\Data\Activity\StaffActivityController::class)->name('activity.staff');
        Route::get('visitors', \App\Http\Controllers\Data\Activity\VisitorActivityController::class)->name('activity.visitors');
        Route::get('company-vehicles', [\App\Http\Controllers\Data\Activity\VehicleActivityController::class, 'company'])->name('activity.vehicles');
    });

    // Visitors
    Route::prefix('visitors')->group(function () {
        Route::get('', \App\Http\Controllers\Data\Visitors\GetVisitorsController::class)->name('visitors');
        Route::get('{visitor_id}', [\App\Http\Controllers\Data\Visitors\SingleVisitorController::class, 'get'])->name('visitors.single');
    });


    // Exports
    Route::prefix('exports')->group(function () {
        Route::get('logins', [\App\Http\Controllers\Data\History\LoginHistoryController::class, 'export'])->name('exports.logins');
        Route::get('visits', [\App\Http\Controllers\Data\Activity\VisitorActivityController::class, 'export'])->name('exports.visits');
        Route::get('visitors', [\App\Http\Controllers\Data\Visitors\GetVisitorsController::class, 'export'])->name('exports.visitors');
        Route::get('visits/by/{visitor_id}', [\App\Http\Controllers\Data\Visitors\SingleVisitorController::class, 'export'])->name('exports.visits.single');
        Route::get('staff-activity', [\App\Http\Controllers\Data\Activity\StaffActivityController::class, 'export'])->name('exports.checkins');
        Route::get('company_vehicles', [\App\Http\Controllers\Data\Vehicles\GetVehiclesController::class, 'exportCompany'])->name('exports.vehicles');
        Route::get('company_vehicles/activity', [\App\Http\Controllers\Data\Activity\VehicleActivityController::class, 'exportCompany'])->name('exports.company_vehicles_activity');
        Route::get('other_vehicles', [\App\Http\Controllers\Data\Vehicles\GetVehiclesController::class, 'exportOther'])->name('exports.other_vehicles');
        Route::get('other_vehicles/{vehicle_id}/activity', [\App\Http\Controllers\Data\Vehicles\SingleVehicleController::class, 'exportOtherActivity'])->name('exports.other_vehicles_activity');
    });

    // Imports
    Route::prefix('imports')->group(function () {
        Route::post('staff/{site_id}/{company_id}', [\App\Http\Controllers\Data\Staff\AddStaffController::class, 'import'])->name('imports.staff');
    });

});
