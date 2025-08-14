<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirpayController;

Route::get('/', [AirpayController::class, 'showPaymentForm'])->name('airpay.form');
Route::post('/process-payment', [AirpayController::class, 'processPayment'])->name('airpay.process');
Route::post('/payment-response', [AirpayController::class, 'handleResponse'])->name('airpay.response');