@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Editar Tarea</h4>
    </div>
    <div class="card-body">
        <form id="form-editar">
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" id="titulo" class="form-control" required>
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

                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Vencimiento</label>
                    <input type="date" id="fecha_vencimiento" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Prioridad</label>
                    <select id="prioridad" class="form-select">
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Estado</label>
                    <select id="estado" class="form-select">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completada">Completada</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-warning">Actualizar (API)</button>
            <a href="{{ route('tareas.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<script>
    const idTarea = "{{ $id }}";

    document.addEventListener('DOMContentLoaded', function() {
        
        
        fetch('/api/categorias')
            .then(res => res.json())
            .then(categorias => {
                const select = document.getElementById('categoria_id');
                select.innerHTML = '<option value="">Selecciona una categoría...</option>';
                
                categorias.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    select.appendChild(option);
                });

                return fetch(`/api/tareas/${idTarea}`);
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('titulo').value = data.titulo;
                document.getElementById('descripcion').value = data.descripcion;
                document.getElementById('fecha_vencimiento').value = data.fecha_vencimiento;
                document.getElementById('prioridad').value = data.prioridad;
                document.getElementById('estado').value = data.estado;
                
                document.getElementById('categoria_id').value = data.categoria_id;
            })
            .catch(error => console.error('Error cargando datos:', error));
    });

    document.getElementById('form-editar').addEventListener('submit', function(e) {
        e.preventDefault(); 

        const datos = {
            titulo: document.getElementById('titulo').value,
            descripcion: document.getElementById('descripcion').value,
            fecha_vencimiento: document.getElementById('fecha_vencimiento').value,
            prioridad: document.getElementById('prioridad').value,
            estado: document.getElementById('estado').value,
            categoria_id: document.getElementById('categoria_id').value,
        };

        fetch(`/api/tareas/${idTarea}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if(response.ok) {
                alert('¡Tarea actualizada con éxito vía API!');
                window.location.href = "/tareas";
            } else {
                alert('Error al actualizar');
            }
        });
    });
</script>
@endsection