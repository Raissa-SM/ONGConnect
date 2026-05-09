<?php

use App\Http\Controllers\Web\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/',                     [HomeController::class, 'index'])->name('home');
Route::get('/login',                [HomeController::class, 'login'])->name('login');
Route::get('/registro',             [HomeController::class, 'registro'])->name('registro');
Route::post('/logout',              [HomeController::class, 'logout'])->name('logout');
Route::get('/dashboard/voluntario', [HomeController::class, 'dashboardVoluntario'])->name('dashboard.voluntario');
Route::get('/dashboard/ong',        [HomeController::class, 'dashboardOng'])->name('dashboard.ong');
