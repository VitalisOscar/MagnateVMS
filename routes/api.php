<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Staff\StaffController;
use App\Http\Controllers\Api\VisitEnquiryController;
use App\Http\Controllers\Api\Vehicle\VehicleCheckInController;
use App\Http\Controllers\Api\Vehicle\VehicleCheckOutController;
use App\Http\Controllers\Api\Vehicle\GetVehiclesController;
use App\Http\Controllers\Api\Visitor\VisitorCheckInController;
use App\Http\Controllers\Api\Visitor\VisitorCheckOutController;
use App\Http\Controllers\Api\Visitor\VisitorEnquiryController;
use App\Http\Controllers\Api\Staff\StaffCheckInController;
use App\Http\Controllers\Api\Staff\StaffCheckOutController;
use App\Http\Controllers\Api\Staff\StaffRecordsController;
use App\Http\Controllers\Api\SummaryController;
use App\Http\Controllers\Api\Vehicle\GetDriversController;
use App\Http\Controllers\Api\Vehicle\VehicleRecordsController;
use App\Http\Controllers\Api\Visitor\AccessCardController;
use App\Http\Controllers\Api\Visitor\VisitorRecordsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('api.login');
});

Route::middleware('auth:sanctum')->group(function(){
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

        Route::get('staff', [StaffController::class, 'getAll'])->name('api.get.all.staff');
        Route::get('staff/in', [StaffController::class, 'getCheckedIn'])->name('api.get.checkins.staff');
        Route::get('visitors/in', [VisitorEnquiryController::class, 'getCheckedIn'])->name('api.get.checkins.visitors');

        Route::get('summary', [SummaryController::class, 'basic'])->name('api.get.summary');
        Route::get('sites', [SummaryController::class, 'getSites'])->withoutMiddleware('auth:sanctum')->name('api.get.sites');

        Route::get('company-vehicles', [GetVehiclesController::class, 'company'])->name('api.get.vehicles.company');
        Route::get('drivers', GetDriversController::class)->name('api.get.drivers');
    });

    Route::prefix('history')->group(function(){
        Route::get('visitors', VisitorRecordsController::class)->name('api.records.visitor');
        Route::get('staff', StaffRecordsController::class)->name('api.records.staff');
        Route::get('vehicles', VehicleRecordsController::class)->name('api.records.vehicles');
    });

    Route::prefix('cards')->group(function(){
        Route::post('issue', [AccessCardController::class, 'issueCard'])->name('api.cards.issue');
        Route::get('get-without', [AccessCardController::class, 'getVisitorsWithoutCard'])->name('api.cards.get_without');
    });

    Route::post('user/password', [AuthController::class, 'changePassword'])->name('api.password');

});
