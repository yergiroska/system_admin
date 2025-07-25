@extends('layouts.app')

@section('content')
    <h1>Logs del sistema</h1>

    <table border="1">
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Acción</th>
            <th>Usuario</th>
            <th>IP</th>
            <th>Ver</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($logs as $log)
            <tr id="{{ $log->id }}">
                <td>{{ $log->id }}</td>
                <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->name_user }}</td>
                <td>{{ $log->ip }}</td>
                <td><a href="{{ route('logs.details', $log->id) }}">Ver</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#delete').on('click', function(evento) {
                evento.preventDefault();
                let id = $(this).attr('data-id') // esta y la de abajohacen lo mismo
                let url = $(this).data('url')
                if(confirm('Estas seguro')) {
                    $.ajax({
                        url: url,
                        method: 'GET',
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
