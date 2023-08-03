<?php

use App\Http\Controllers\Api\CekWaktuPresensiController;
use App\Http\Controllers\Api\PresensiLemburController;
use App\Http\Controllers\Api\PresensiMasukController;
use App\Http\Controllers\Api\PresensiPulangController;
use App\Http\Controllers\Api\PresensiTerlambatController;
use App\Http\Controllers\Api\RiwayatPresensiController;
use App\Http\Controllers\ApiAttendanceController;
use App\Http\Controllers\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/v1')->group(function(){
    Route::post('/login', [ApiAuthController::class, 'login'])->name('v1.auth.login');

    Route::middleware('auth:sanctum')->group(function(){
        // Get user profile
        Route::get('/user', fn(Request $request) => $request->user())->name('v1.auth.userProfile');

        // Delete token or logout
        Route::post('/logout', [ApiAuthController::class, 'logout'])->name('v1.auth.logout');

        Route::apiResource('attendances', ApiAttendanceController::class, ['names' => 'v1.attendances'])->only(['index', 'store']);

        // Cek waktu presensi
        Route::post('/checkwaktupresensi', CekWaktuPresensiController::class)->name('cekWaktuPresensi');

        // Presensi masuk
        Route::post('presensi/masuk', PresensiMasukController::class)->name('v1.presensi.masuk');

        // presensi terlambat
        Route::post('presensi/terlambat', PresensiTerlambatController::class)->name('v1.presensi.terlambat');

        // Presensi pulang
        Route::post('presensi/pulang', PresensiPulangController::class)->name('v1.presensi.pulang');

        // Presensi lembur
        Route::post('/presensi/lembur', PresensiLemburController::class)->name('v1.presensi.lembur');

        // Riwayat presensi
        Route::get('/presensi/riwayat', RiwayatPresensiController::class)->name('v1.presensi.riwayat');
    });
});
