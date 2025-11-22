<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subtarea;
use Illuminate\Http\Request;

class SubtareaApiController extends Controller
{
    // POST
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|max:255',
            'tarea_id' => 'required|exists:tareas,id'
        ]);

        $subtarea = Subtarea::create([
            'titulo' => $request->titulo,
            'tarea_id' => $request->tarea_id,
            'completada' => false
        ]);

        return response()->json($subtarea, 201);
    }

    // PATCH 
    public function toggle($id)
    {
        $subtarea = Subtarea::findOrFail($id);
        $subtarea->update(['completada' => !$subtarea->completada]);
        
        return response()->json($subtarea, 200);
    }

    // DELETE
    public function destroy($id)
    {
        $subtarea = Subtarea::findOrFail($id);
        $subtarea->delete();
        
        return response()->json(['message' => 'Subtarea eliminada'], 200);
    }
}