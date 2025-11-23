<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TareaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('tareas.index') : view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::resource('categorias', CategoriaController::class);
    Route::resource('tareas', TareaController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return redirect()->route('tareas.index');
})->middleware(['auth'])->name('dashboard');
require __DIR__.'/auth.php';