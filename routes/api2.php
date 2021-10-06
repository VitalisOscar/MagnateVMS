<?php

use App\Http\Controllers\CheckIn\StaffCheckInController;
use App\Http\Controllers\CheckIn\VehicleCheckInController;
use App\Http\Controllers\CheckIn\VisitorCheckInController;
use App\Http\Controllers\CheckOut\StaffCheckOutController;
use App\Http\Controllers\CheckOut\VehicleCheckOutController;
use App\Http\Controllers\CheckOut\VisitorCheckOutController;
use App\Http\Controllers\Data\Activity\StaffActivityController;
use App\Http\Controllers\Data\Activity\VehicleActivityController;
use App\Http\Controllers\Data\Activity\VisitorActivityController;
use App\Http\Controllers\Data\Drivers\GetDriversController;
use App\Http\Controllers\Data\Sites\GetSitesController;
use App\Http\Controllers\Data\Staff\GetStaffController;
use App\Http\Controllers\Data\Vehicles\GetVehiclesController;
use App\Http\Controllers\Data\Visitors\SingleVisitorController;
use Illuminate\Support\Facades\Route;

Route::prefix('activity')
->name('activity.')
->group(function (){
    // Checkin
    Route::post('checkin/visitor', VisitorCheckInController::class)->name('checkin.visitor');
    Route::post('checkin/staff', StaffCheckInController::class)->name('checkin.visitor');
    Route::post('checkin/vehicle', VehicleCheckInController::class)->name('checkin.vehicle');

    // Check out
    Route::post('checkout/visitor', VisitorCheckOutController::class)->name('checkout.visitor');
    Route::post('checkout/staff', StaffCheckOutController::class)->name('checkout.staff');
    Route::post('checkout/vehicle', VehicleCheckOutController::class)->name('checkout.vehicle');
});

Route::prefix('get/activity')
->name('history.')
->group(function (){
    Route::get('visitors', VisitorActivityController::class)->name('visitors');
    Route::get('staff', StaffActivityController::class)->name('staff');
    Route::get('vehicles', [VehicleActivityController::class, 'company'])->name('vehicles');
});

Route::prefix('get')
->name('data.')
->group(function (){
    Route::get('sites', GetSitesController::class)->name('sites');
    Route::get('staff', [GetStaffController::class, 'atSite'])->name('staff');
    Route::get('drivers', [GetDriversController::class, 'all'])->name('drivers');
    Route::get('company-vehicles', [GetVehiclesController::class, 'company'])->name('company_vehicles');

    Route::get('checked-in/staff', [StaffActivityController::class, 'getCheckedIn'])->name('staff.checked_in');
    Route::get('checked-in/visitors', [VisitorActivityController::class, 'getCheckedIn'])->name('visitors.checked_in');

    Route::get('visitor/by-id', [SingleVisitorController::class, 'getByIdNumber'])->name('visitor.get_by_id');
});
