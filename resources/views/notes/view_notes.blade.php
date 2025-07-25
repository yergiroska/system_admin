@extends('layouts.app')

@section('content')
    <h1>Lista de Notas</h1>
    <table border="1">
        <thead>
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
                    let $tr='';
                    for (const note of notes) {
                        $tr += '<tr>';
                        $tr += '<td>'+note.id+'</td>';
                        $tr += '<td>'+note.title+'</td>';
                        $tr += '<td>'+note.contents+'</td>';
                        $tr += '<td>' + (note.completed ? 'Yes' : 'No') + '</td>';
                        //$tr += '<td>'+note.completed+'</td>';
                        $tr += '<td>' + (note.completed ? 'Si' : 'No') + '</td>';
                        $tr += '<td><a href="/notes/' + note.id + '">Ver</a></td>';
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
