@extends('layouts.app')
@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-list"></i> Lista de Notas</h2>
        </div>

        {{-- Tabla de empresas --}}
        <div class="card shadow">
            <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
                <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Contenido</th>
                    <th>Completado</th>
                    <th>Acción</th>
                </tr>
                </thead>
                <tbody class="notes"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $.ajax({
                url: '{{ route('notes.lists') }}',
                method: 'GET',
                success: function (response) {
                    let notes = response.data
                    let $tr;
                    for (const note of notes) {
                        $tr += '<tr>';
                        $tr += '<td>'+note.id+'</td>';
                        $tr += '<td>'+note.title+'</td>';
                        $tr += '<td>'+note.contents+'</td>';
                        $tr += '<td>' + (note.completed ? 'Si' : 'No') + '</td>';
                        $tr += '<td><a href="' + note.url_detail + '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>';
                        $tr += '</tr>';
                    }
                    $('.notes').append($tr)
                },
                error: function (xhr) {
                    // Manejar errores
                }
            });
        });
    </script>
@endsection
