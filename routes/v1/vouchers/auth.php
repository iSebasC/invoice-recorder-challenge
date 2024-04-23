<?php

use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use App\Http\Controllers\Vouchers\Voucher\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\Voucher\GetVoucherHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::get('/filter', [GetVouchersHandler::class, 'filter']);
        Route::get('/total-amounts', [GetVouchersHandler::class, 'getTotalAmounts']);
        Route::post('/', StoreVouchersHandler::class);
        Route::put('/{id}', [StoreVouchersHandler::class, 'update']);
        Route::delete('/{id}', [StoreVouchersHandler::class, 'destroy']);
    }
);
