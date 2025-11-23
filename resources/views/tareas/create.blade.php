@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Nueva Tarea</h4>
    </div>
    <div class="card-body">
        
        <form id="form-crear">
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" id="titulo" class="form-control" placeholder="Ej: Comprar leche" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría</label>
                    <select id="categoria_id" class="form-select" required>
                        <option value="">Cargando...</option>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea id="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha Vencimiento</label>
                    <input type="date" id="fecha_vencimiento" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioridad</label>
                    <select id="prioridad" class="form-select">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Guardar (API)</button>
            <a href="{{ route('tareas.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<script>
    
    const userId = "{{ Auth::id() }}"; 

    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/categorias')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('categoria_id');
                select.innerHTML = '<option value="">Selecciona una categoría...</option>';
                data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    select.appendChild(option);
                });
            });
    });

    document.getElementById('form-crear').addEventListener('submit', function(e) {
        e.preventDefault();

        const datos = {
            titulo: document.getElementById('titulo').value,
            descripcion: document.getElementById('descripcion').value,
            fecha_vencimiento: document.getElementById('fecha_vencimiento').value,
            prioridad: document.getElementById('prioridad').value,
            categoria_id: document.getElementById('categoria_id').value,
            creador_id: userId 
        };

        fetch('/api/tareas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (response.ok) {
                window.location.href = "/tareas";
            } else {
                alert('Error al crear la tarea. Revisa los datos.');
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endsection