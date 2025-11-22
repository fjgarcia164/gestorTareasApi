<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TareaApiController;
use App\Models\Categoria;
use App\Http\Controllers\Api\SubtareaApiController;
use App\Http\Controllers\Api\ComentarioApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('tareas', TareaApiController::class)
     ->names('api.tareas');

Route::get('/categorias', function () {
    return response()->json(Categoria::all(), 200);
});
Route::post('/subtareas', [SubtareaApiController::class, 'store']);
Route::patch('/subtareas/{id}/toggle', [SubtareaApiController::class, 'toggle']);
Route::delete('/subtareas/{id}', [SubtareaApiController::class, 'destroy']);

Route::post('/comentarios', [ComentarioApiController::class, 'store']);