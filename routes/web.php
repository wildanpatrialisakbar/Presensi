<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceExportController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OffWorkController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn() => Inertia::render('LandingPage/Index'));

Route::middleware(['auth', 'verified'])->group(function (){
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
    Route::get('configurations', [ConfigurationController::class, 'index'])->name('configurations.index');
    Route::put('configurations/{configuration}', [ConfigurationController::class, 'update'])->name('configurations.update');
    Route::resources([
        'locations' => LocationController::class,
        'users' => UserController::class,
        'offworks' => OffWorkController::class,
        'attendances' => AttendanceController::class
    ]);

    // Update status pengajuan cuti
    Route::put('/offworks/{offwork}/updateStatus', [OffWorkController::class, 'updateStatus'])->name('offworks.updateStatus');

    // export spesifik data presensi
    Route::get('/export/attendances/specific', [AttendanceExportController::class, 'specific'])->name('export.attendances.specific');
});

Route::get('/info', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

require __DIR__.'/auth.php';
