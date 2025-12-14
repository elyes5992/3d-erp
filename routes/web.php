<?php

use App\Http\Controllers\ErpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ErpController::class, 'index'])->name('erp.index');
Route::post('/product', [ErpController::class, 'storeProduct'])->name('erp.product.store');
Route::post('/category', [ErpController::class, 'storeCategory'])->name('erp.category.store');
Route::post('/task', [ErpController::class, 'storeTask'])->name('erp.task.store');
Route::get('/task/{id}/toggle', [ErpController::class, 'toggleTask'])->name('erp.task.toggle');


Route::get('/debug-db', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        $driver = DB::connection()->getDriverName();
        $host = config('database.connections.pgsql.host');
        
        return response()->json([
            'STATUS' => 'Connected successfully',
            'DRIVER_USED' => $driver, // If this says 'sqlite', that is the problem
            'DATABASE_NAME' => $dbName,
            'CONFIGURED_HOST' => $host,
            'ENVIRONMENT_VAR_CHECK' => [
                'DB_CONNECTION' => env('DB_CONNECTION', 'NOT SET'),
                'DB_HOST' => env('DB_HOST', 'NOT SET'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'STATUS' => 'Connection Failed',
            'ERROR' => $e->getMessage()
        ]);
    }});