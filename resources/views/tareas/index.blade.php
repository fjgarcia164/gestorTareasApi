@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Mis Tareas (Cargadas vía API)</h2>
    <a href="{{ route('tareas.create') }}" class="btn btn-primary">+ Nueva Tarea</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Título</th>
                    <th>Categoría</th>
                    <th>Vencimiento</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="contenedor-tareas">
                </tbody>
        </table>
        
        <div id="loading" class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando datos de la API...</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarTareas();
    });

    function cargarTareas() {
        fetch('/api/tareas')
            .then(response => response.json()) 
            .then(data => {
                document.getElementById('loading').style.display = 'none';
                const tbody = document.getElementById('contenedor-tareas');
                tbody.innerHTML = ''; 
                
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center p-4">No hay tareas (API vacía)</td></tr>';
                    return;
                }

                data.forEach(tarea => {
                    const fila = `
                        <tr>
                            <td class="fw-bold">${tarea.titulo}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    ${tarea.categoria ? tarea.categoria.nombre : 'Sin Categ.'}
                                </span>
                            </td>
                            <td>${tarea.fecha_vencimiento || '—'}</td>
                            <td>
                                <span class="badge ${getPrioridadColor(tarea.prioridad)}">
                                    ${tarea.prioridad.toUpperCase()}
                                </span>
                            </td>
                            <td>${tarea.estado}</td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="/tareas/${tarea.id}" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                    
                                    <a href="/tareas/${tarea.id}/edit" class="btn btn-sm btn-outline-secondary">
                                        Editar
                                    </a>

                                    <button onclick="borrarTarea(${tarea.id})" class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += fila;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading').innerHTML = '<p class="text-danger">Error al cargar la API</p>';
            });
    }

    function borrarTarea(id) {
        if(!confirm('¿Estás seguro de que quieres borrar esta tarea?')) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/api/tareas/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => {
            if(response.ok) {
                alert('Tarea eliminada correctamente');
                
                document.getElementById('loading').style.display = 'block';
                cargarTareas(); 
            } else {
                alert('Hubo un error al borrar la tarea');
            }
        })
        .catch(error => console.error('Error al borrar:', error));
    }

    function getPrioridadColor(prioridad) {
        if(prioridad === 'alta') return 'bg-danger';
        if(prioridad === 'media') return 'bg-warning text-dark';
        return 'bg-success';
    }
</script>
@endsection