<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// Públicas: serviços e agendamento pelo cliente
Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

// Login do barbeiro (limite de 5 tentativas por minuto)
Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->middleware('throttle:5,1');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');

// Somente barbeiro: remarcar, cancelar e cadastrar serviços
Route::middleware(EnsureAdmin::class)->group(function () {
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
});
