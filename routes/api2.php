<?php

use App\Http\Controllers\CheckIn\StaffCheckInController;
use App\Http\Controllers\CheckIn\VehicleCheckInController;
use App\Http\Controllers\CheckIn\VisitorCheckInController;
use App\Http\Controllers\CheckOut\StaffCheckOutController;
use App\Http\Controllers\CheckOut\VehicleCheckOutController;
use App\Http\Controllers\CheckOut\VisitorCheckOutController;
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
