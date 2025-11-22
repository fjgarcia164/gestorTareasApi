<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
            'tarea_id' => 'required|exists:tareas,id',
        ]);

        $comentario = Comentario::create([
            'contenido' => $request->contenido,
            'tarea_id' => $request->tarea_id,
           
            'user_id' => Auth::id() ?? $request->user_id ?? 1
        ]);

        return response()->json($comentario->load('user'), 201);
    }
}