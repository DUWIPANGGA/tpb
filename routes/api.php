<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BarangController;
use App\Http\Controllers\Api\V1\KeranjangController;
use App\Http\Controllers\Api\V1\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'ormawa.api', 'throttle:10,1'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        Route::get('/barang', [BarangController::class, 'index']);
        Route::get('/barang/{id}', [BarangController::class, 'show'])->whereNumber('id');

        Route::get('/keranjang', [KeranjangController::class, 'index']);
        Route::post('/keranjang', [KeranjangController::class, 'store']);
        Route::put('/keranjang/{id}', [KeranjangController::class, 'update'])->whereNumber('id');
        Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])->whereNumber('id');

        Route::get('/permohonan', [TransaksiController::class, 'permohonanIndex']);
        Route::post('/permohonan/submit', [TransaksiController::class, 'submitPermohonan']);

        Route::get('/pengembalian', [TransaksiController::class, 'pengembalianIndex']);
        Route::post('/pengembalian/{permohonanId}', [TransaksiController::class, 'submitPengembalian'])
            ->whereNumber('permohonanId');

        Route::get('/riwayat', [TransaksiController::class, 'riwayat']);
        Route::get('/sync/status', [TransaksiController::class, 'sync']);
    });
});
