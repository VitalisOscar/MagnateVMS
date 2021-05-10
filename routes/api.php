<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Staff\StaffVisitEnquiryController;
use App\Http\Controllers\Api\VisitEnquiryController;
use App\Http\Controllers\Api\Vehicle\VehicleCheckInController;
use App\Http\Controllers\Api\Vehicle\VehicleCheckOutController;
use App\Http\Controllers\Api\Visitor\VisitorCheckInController;
use App\Http\Controllers\Api\Visitor\VisitorCheckOutController;
use App\Http\Controllers\Api\Visitor\VisitorEnquiryController;
use App\Http\Controllers\Api\Staff\StaffCheckInController;
use App\Http\Controllers\Api\Staff\StaffCheckOutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
});

//Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('check')->group(function(){
        Route::post('in/visitor', VisitorCheckInController::class)->name('api.visitor.check.in');
        Route::post('out/visitor', VisitorCheckOutController::class)->name('api.visitor.check.out');

        Route::post('in/vehicle', VehicleCheckInController::class)->name('api.vehicle.check.in');
        Route::post('out/vehicle', VehicleCheckOutController::class)->name('api.vehicle.check.out');

	Route::post('in/staff', StaffCheckInController::class)->name('api.staff.check.in');
        Route::post('out/staff', StaffCheckOutController::class)->name('api.staff.check.out');
    });

    Route::prefix('get')->group(function(){
        Route::get('visitor', [VisitEnquiryController::class, 'getVisitor'])->name('api.get.visitor');
        Route::get('visit', [VisitEnquiryController::class, 'getVisitForCheckOut'])->name('api.get.visit');

        Route::get('staff/in', [StaffVisitEnquiryController::class, 'getCheckedIn'])->name('api.get.checkins.staff');
	Route::get('visitors/in', [VisitorEnquiryController::class, 'getCheckedIn'])->name('api.get.checkins.visitors');
    });

//});
