<?php

use App\Http\Controllers\TaskController;

Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::patch('/tasks/{task}/details', [TaskController::class, 'updateDetails'])->name('tasks.updateDetails');
Route::patch('/tasks/{task}/category', [TaskController::class, 'updateCategory'])->name('tasks.updateCategory');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

