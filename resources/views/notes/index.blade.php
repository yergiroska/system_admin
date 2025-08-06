@extends('layouts.app')

@section('content')
    <h1>Notas</h1>
    <a href="{{ route('notes.create') }}">Crear Nota</a> |
    <a href="{{ route('notes.view') }}">Lista de Notas</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table>
        <thead>
        <tr>
            <th>Título</th>
            <th>Contenido</th>
            <th>Completado</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($note as $note)
            <tr id="{!! $note->getId() !!}">
                <td>{{ $note->getTitle() }}</td>
                <td>{{ $note->getContents() }}</td>
                <td>{{ $note->getCompleted() ? 'Si' : 'No' }}</td>
                <td>
                    <a href="{{ route('notes.edit', $note) }}">Editar</a>
                    <button data-id="{!! $note->getId() !!}"
                            data-url="{!! route('notes.destroy', $note->getId()) !!}"
                            type="button" class="delete">Eliminar</button>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.delete').on('click', function(evento) {
                evento.preventDefault();
                let id = $(this).attr('data-id') // esta y la de abajohacen lo mismo
                let url = $(this).data('url')

                if(confirm('Estas seguro')) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{!!  csrf_token() !!}'
                        },
                        success: function(response) {
                            if(response.status === 'success'){
                                alert(response.message);
                                $('tr#'+id).remove()
                            }
                        },
                        error: function(xhr) {
                            // Manejar errores
                        }
                    });
                }
            })
        });
    </script>
@endsection

