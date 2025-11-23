<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class TareaApiController extends Controller
{
    // GET
  // GET /api/tareas
    public function index()
    {
       
        $tareas = Tarea::where('creador_id', Auth::id())
                        ->with('categoria')
                        ->get();

        return response()->json($tareas, 200);
    }

    // POST
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'prioridad' => 'required',
            'creador_id' => 'required|exists:users,id' 
        ]);

        $tarea = Tarea::create($request->all());

        return response()->json([
            'message' => 'Tarea guardada correctamente',
            'tarea' => $tarea
        ], 201);
    }

    // GET
    public function show(string $id)
    {
        $tarea = Tarea::with(['categoria', 'subtareas', 'comentarios'])->find($id);

        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        return response()->json($tarea, 200);
    }

    // PUT
    public function update(Request $request, string $id)
    {
        $tarea = Tarea::find($id);
        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        $tarea->update($request->all());
        return response()->json(['message' => 'Tarea actualizada', 'tarea' => $tarea], 200);
    }

    // DELETE
    public function destroy(string $id)
    {
        $tarea = Tarea::find($id);
        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        $tarea->delete();
        return response()->json(['message' => 'Tarea eliminada'], 200);
    }
}