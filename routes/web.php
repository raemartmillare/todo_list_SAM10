<?php

use App\Http\Controllers\TaskController;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::patch('/tasks/{task}/edit', [TaskController::class, 'updateDetails'])->name('tasks.updateDetails');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
