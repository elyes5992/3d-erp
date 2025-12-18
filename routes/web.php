<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\KanbanController;

// --- Dashboard & Journal ---
Route::get('/', [DashboardController::class, 'index'])->name('erp.dashboard');
Route::post('/journal/update', [DashboardController::class, 'updateJournal'])->name('erp.journal.update');

// --- Products ---
Route::get('/products', [ProductController::class, 'index'])->name('erp.products.index');
Route::post('/products', [ProductController::class, 'store'])->name('erp.products.store');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('erp.products.update');
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('erp.products.destroy');

// --- Categories ---
Route::get('/categories', [CategoryController::class, 'index'])->name('erp.categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('erp.categories.store');
Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('erp.categories.update');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('erp.categories.destroy');

// --- Tasks (Planning) ---
Route::get('/planning', [TaskController::class, 'index'])->name('erp.planning.index');
Route::post('/planning', [TaskController::class, 'store'])->name('erp.planning.store');
Route::put('/planning/{id}', [TaskController::class, 'update'])->name('erp.planning.update');
Route::delete('/planning/{id}', [TaskController::class, 'destroy'])->name('erp.planning.destroy');
Route::get('/planning/{id}/toggle', [TaskController::class, 'toggle'])->name('erp.planning.toggle');

// --- Subtasks ---
Route::post('/tasks/{id}/subtasks', [SubtaskController::class, 'store'])->name('erp.subtasks.store');
Route::delete('/subtasks/{id}', [SubtaskController::class, 'destroy'])->name('erp.subtasks.destroy');
Route::get('/subtasks/{id}/toggle', [SubtaskController::class, 'toggle'])->name('erp.subtasks.toggle');

// --- Kanban ---
Route::get('/kanban', [KanbanController::class, 'index'])->name('erp.kanban.index');
Route::post('/kanban/update-status', [KanbanController::class, 'updateStatus'])->name('erp.kanban.update');