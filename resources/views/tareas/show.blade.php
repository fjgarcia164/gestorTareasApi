@extends('layouts.app')

@section('content')
<div id="loading" class="text-center p-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-2">Cargando detalles de la tarea...</p>
</div>

<div id="main-content" class="row" style="display: none;">
    
    <div class="col-md-8">
        
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" id="t-titulo"></h4> <span class="badge bg-light text-dark" id="t-estado"></span>
            </div>
            <div class="card-body">
                <p class="lead" id="t-descripcion"></p>
                <hr>
                <div class="row text-muted small">
                    <div class="col-md-4">Categoría: <strong id="t-categoria"></strong></div>
                    <div class="col-md-4">Vencimiento: <strong id="t-fecha"></strong></div>
                    <div class="col-md-4">Prioridad: <strong id="t-prioridad"></strong></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('tareas.index') }}" class="btn btn-secondary">Volver</a>
                <a id="btn-editar-link" href="#" class="btn btn-warning">Editar Datos</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                <span>📋 Subtareas</span>
                <span class="badge bg-secondary" id="contador-subtareas">0</span>
            </div>
            <div class="card-body">
                <form id="form-subtarea" class="d-flex gap-2 mb-3">
                    <input type="text" id="input-subtarea" class="form-control" placeholder="Escribe una nueva subtarea..." required>
                    <button type="submit" class="btn btn-success">Añadir</button>
                </form>

                <ul class="list-group list-group-flush" id="lista-subtareas">
                    </ul>
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">💬 Comentarios</div>
            
            <div class="card-body bg-white" id="lista-comentarios" style="max-height: 500px; overflow-y: auto; background-color: #f8f9fa;">
                </div>

            <div class="card-footer bg-white">
                <form id="form-comentario">
                    <div class="mb-2">
                        <textarea id="input-comentario" class="form-control" rows="2" placeholder="Escribe algo..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-sm">Enviar Comentario</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const idTarea = "{{ $id }}";
    
    const currentUserId = "{{ Auth::id() }}"; 
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', () => {
        cargarTodo();
    });

    function cargarTodo() {
        fetch(`/api/tareas/${idTarea}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('t-titulo').textContent = data.titulo;
                document.getElementById('t-descripcion').textContent = data.descripcion || 'Sin descripción';
                document.getElementById('t-estado').textContent = data.estado;
                document.getElementById('t-categoria').textContent = data.categoria ? data.categoria.nombre : 'General';
                document.getElementById('t-fecha').textContent = data.fecha_vencimiento || '-';
                document.getElementById('t-prioridad').textContent = data.prioridad.toUpperCase();
                
                document.getElementById('btn-editar-link').href = `/tareas/${idTarea}/edit`;

                pintarSubtareas(data.subtareas);

                pintarComentarios(data.comentarios);

                document.getElementById('loading').style.display = 'none';
                document.getElementById('main-content').style.display = 'flex';
            })
            .catch(err => console.error(err));
    }

    function pintarSubtareas(subtareas) {
        const lista = document.getElementById('lista-subtareas');
        document.getElementById('contador-subtareas').textContent = subtareas.length;
        lista.innerHTML = '';

        if (subtareas.length === 0) {
            lista.innerHTML = '<li class="list-group-item text-center text-muted fst-italic">No hay subtareas</li>';
            return;
        }

        subtareas.forEach(sub => {
            const tachado = sub.completada ? 'text-decoration-line-through text-muted' : '';
            const btnCheckClass = sub.completada ? 'btn-success' : 'btn-outline-secondary';
            const iconoCheck = sub.completada ? '✔' : ' ';

            lista.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button onclick="toggleSubtarea(${sub.id})" class="btn btn-sm ${btnCheckClass} me-2" style="width: 30px; height: 30px; padding: 0; line-height: 0;">
                            ${iconoCheck}
                        </button>
                        <span class="${tachado}">${sub.titulo}</span>
                    </div>
                    <button onclick="borrarSubtarea(${sub.id})" class="btn btn-sm text-danger border-0" title="Eliminar">
                        &times;
                    </button>
                </li>
            `;
        });
    }

    document.getElementById('form-subtarea').addEventListener('submit', (e) => {
        e.preventDefault();
        const titulo = document.getElementById('input-subtarea').value;

        fetch('/api/subtareas', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify({ titulo: titulo, tarea_id: idTarea })
        }).then(() => {
            document.getElementById('input-subtarea').value = '';
            cargarTodo();
        });
    });

    window.toggleSubtarea = (id) => {
        fetch(`/api/subtareas/${id}/toggle`, { method: 'PATCH' })
            .then(() => cargarTodo());
    };

    window.borrarSubtarea = (id) => {
        if(!confirm('¿Borrar subtarea?')) return;
        fetch(`/api/subtareas/${id}`, { method: 'DELETE' })
            .then(() => cargarTodo());
    };


    function pintarComentarios(comentarios) {
        const contenedor = document.getElementById('lista-comentarios');
        contenedor.innerHTML = '';

        if (comentarios.length === 0) {
            contenedor.innerHTML = '<p class="text-center text-muted small mt-3">Sé el primero en comentar.</p>';
            return;
        }

        comentarios.forEach(com => {
            const autor = com.user ? com.user.name : 'Usuario';
            const fecha = new Date(com.created_at).toLocaleDateString();

            contenedor.innerHTML += `
                <div class="mb-3 pb-2 border-bottom">
                    <div class="d-flex justify-content-between">
                        <strong style="font-size: 0.9rem;">${autor}</strong>
                        <small class="text-muted" style="font-size: 0.75rem;">${fecha}</small>
                    </div>
                    <div class="mt-1 text-secondary" style="font-size: 0.9rem;">
                        ${com.contenido}
                    </div>
                </div>
            `;
        });
    }

    document.getElementById('form-comentario').addEventListener('submit', (e) => {
        e.preventDefault();
        const contenido = document.getElementById('input-comentario').value;

        fetch('/api/comentarios', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                contenido: contenido, 
                tarea_id: idTarea,
                user_id: currentUserId
            })
        }).then(res => {
            if(res.ok) {
                document.getElementById('input-comentario').value = '';
                cargarTodo();
            } else {
                alert('Error al enviar comentario');
            }
        });
    });

</script>
@endsection