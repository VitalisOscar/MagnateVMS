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
    Route::post('checkout/vehicle', [VehicleCheckOutController::class, 'company'])->name('checkout.vehicle');

    // Get data
    Route::get('get/activity/visitors')->name('visitors');
});

Route::prefix('get/activity')
->name('history.')
->group(function (){
    Route::get('visitors', VisitorActivityController::class)->name('visitors');
    Route::get('staff', StaffActivityController::class)->name('staff');
    Route::get('vehicles', VehicleActivityController::class)->name('vehicles');
});
