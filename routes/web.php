<?php

use App\Http\Controllers\ErpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ErpController::class, 'index'])->name('erp.index');
Route::post('/product', [ErpController::class, 'storeProduct'])->name('erp.product.store');
Route::post('/category', [ErpController::class, 'storeCategory'])->name('erp.category.store');
Route::post('/task', [ErpController::class, 'storeTask'])->name('erp.task.store');
Route::get('/task/{id}/toggle', [ErpController::class, 'toggleTask'])->name('erp.task.toggle');