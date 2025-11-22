@extends('layouts.app')

@section('content')
<div id="loading" class="text-center p-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p>Cargando detalles...</p>
</div>

<div id="detalle-tarea" class="row" style="display: none;">
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h3 class="mb-0 h5" id="t-titulo"></h3>
                <span class="badge bg-light text-dark" id="t-estado"></span>
            </div>
            <div class="card-body">
                <p id="t-descripcion"></p>
                <hr>
                <div class="d-flex justify-content-between text-muted small">
                    <span>Categoría: <strong id="t-categoria"></strong></span>
                    <span>Vencimiento: <strong id="t-vencimiento"></strong></span>
                    <span>Prioridad: <strong id="t-prioridad"></strong></span>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('tareas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const id = "{{ $id }}"; 
        
        fetch(`/api/tareas/${id}`)
            .then(response => response.json())
            .then(tarea => {
                document.getElementById('t-titulo').textContent = tarea.titulo;
                document.getElementById('t-descripcion').textContent = tarea.descripcion || 'Sin descripción';
                document.getElementById('t-estado').textContent = tarea.estado;
                document.getElementById('t-categoria').textContent = tarea.categoria ? tarea.categoria.nombre : 'Sin categoría';
                document.getElementById('t-vencimiento').textContent = tarea.fecha_vencimiento || '—';
                document.getElementById('t-prioridad').textContent = tarea.prioridad.toUpperCase();

                document.getElementById('loading').style.display = 'none';
                document.getElementById('detalle-tarea').style.display = 'flex';
            })
            .catch(error => alert('Error al cargar la tarea'));
    });
</script>
@endsection