<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DispatcherController;
use App\Http\Controllers\Web\Admin\DispatcherCreateRequestController;

Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {


Route::group(['prefix' => 'dispatcher'], function () {
    // Route::get('/', [DispatcherController::class, 'index'])->name('dispatch.dashboard');
    Route::get('/godeye', [DispatcherController::class, 'godseye'])->name('dispatch.godeye');
    Route::get('/', [DispatcherController::class, 'bookride'])->name('dispatch.dashboard');

    //ride request
    Route::get('/rides_request', [DispatcherController::class, 'rideRequest'])->name('dispatch.rideRequest');
    Route::get('/rides_request/list', [DispatcherController::class, 'list'])->name('dispatch.rideRequest.list');
    Route::post('/rides_request/driver/{driver}', [DispatcherController::class, 'driverFind'])->name('dispatch.triprequest.driverFind');
    Route::get('/rides_request/view/{requestmodel}', [DispatcherController::class, 'viewDetails'])->name('dispatch.triprequest.viewDetails');
    Route::get('/rides_request/cancel/{requestmodel}', [DispatcherController::class, 'cancelRide'])->name('dispatch.triprequest.cancel');
    Route::get('/rides_request/download-invoice/{requestmodel}', [DispatcherController::class, 'downloadInvoice']);

    // Track Request
    Route::get('/track/request/{request}', [DispatcherController::class, 'trackRequest'])->name('dispatch.triprequest.trackRequest');

    Route::get('/ongoing_request', [DispatcherController::class, 'ongoingRequest'])->name('dispatch.dispatch.ongoingRequest');
    Route::get('/ongoing_request/find-ride/{request}', [DispatcherController::class, 'ongoingRideDetail'])->name('dispatch.dispatch.ongoingRideDetail');
    Route::get('/ongoing_request/assign/{request}', [DispatcherController::class, 'assignView'])->name('dispatch.dispatch.assignView');
    Route::post('/ongoing_request/assign-driver/{requestmodel}', [DispatcherController::class, 'assignDriver'])->name('dispatch.dispatch.assignDriver');

});
});