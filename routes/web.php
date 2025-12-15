<?php

use App\Http\Controllers\ErpController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Main Dashboard
Route::get('/', [ErpController::class, 'dashboard'])->name('erp.dashboard');

// Products Management
Route::get('/products', [ErpController::class, 'products'])->name('erp.products.index');
Route::post('/products', [ErpController::class, 'storeProduct'])->name('erp.products.store');
Route::put('/products/{id}', [ErpController::class, 'updateProduct'])->name('erp.products.update');
Route::delete('/products/{id}', [ErpController::class, 'destroyProduct'])->name('erp.products.destroy');

// Categories Management
Route::get('/categories', [ErpController::class, 'categories'])->name('erp.categories.index');
Route::post('/categories', [ErpController::class, 'storeCategory'])->name('erp.categories.store');
Route::put('/categories/{id}', [ErpController::class, 'updateCategory'])->name('erp.categories.update');
Route::delete('/categories/{id}', [ErpController::class, 'destroyCategory'])->name('erp.categories.destroy');

// Planning / Tasks Management
Route::get('/planning', [ErpController::class, 'planning'])->name('erp.planning.index');
Route::post('/planning', [ErpController::class, 'storeTask'])->name('erp.planning.store');
Route::put('/planning/{id}', [ErpController::class, 'updateTask'])->name('erp.planning.update');
Route::delete('/planning/{id}', [ErpController::class, 'destroyTask'])->name('erp.planning.destroy');
Route::get('/planning/{id}/toggle', [ErpController::class, 'toggleTask'])->name('erp.planning.toggle');

Route::post('/dashboard/journal', [App\Http\Controllers\ErpController::class, 'updateJournal'])->name('erp.journal.update');

// Kanban View
Route::get('/kanban', [App\Http\Controllers\ErpController::class, 'kanban'])->name('erp.kanban.index');

// API to update status on drag drop
Route::post('/kanban/move', [App\Http\Controllers\ErpController::class, 'updateKanbanStatus'])->name('erp.kanban.move');

// Debug Route
Route::get('/debug-db', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        $driver = DB::connection()->getDriverName();
        $host = config('database.connections.pgsql.host');
        
        return response()->json([
            'STATUS' => 'Connected successfully',
            'DRIVER_USED' => $driver,
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
    }
});