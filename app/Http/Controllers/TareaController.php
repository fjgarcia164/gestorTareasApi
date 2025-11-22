<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    /**
     * Muestra SOLO las tareas del usuario conectado.
     */
    public function index()
    {
        $tareas = Tarea::where('creador_id', Auth::id())
                        ->with(['categoria', 'creador'])
                        ->get();
                        
        return view('tareas.index', compact('tareas'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('tareas.create', compact('categorias'));
    }


public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|max:255',
            'categoria_id' => 'required',
            'prioridad' => 'required',
        ]);

        \App\Models\Tarea::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'prioridad' => $request->prioridad,
            'estado' => 'pendiente',
            'categoria_id' => $request->categoria_id,
            'creador_id' => auth()->id(),
        ]);

        // 3. Redirigir
        return redirect()->route('tareas.index');
    }

  public function show(string $id)
    {
        return view('tareas.show', ['id' => $id]);
    }

   public function edit(string $id)
    {
       
        return view('tareas.edit', ['id' => $id]);
    }
    public function update(Request $request, string $id){}

    public function destroy(string $id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();
        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada.');
    }
}