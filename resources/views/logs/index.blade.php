@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-list"></i> Logs del sistema</h2>
        </div>

        {{-- Tabla de empresas --}}
        <div class="card shadow">
            <div class="card-body">
                <table id="logs-table" class="table table-striped table-hover mb-0 table-responsive table-bordered">
                    <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                        <th>Objeto</th>
                        <th>Objeto ID</th>
                        <th>Usuario</th>
                        <th>Usuario 2</th>
                        <th>Usuario 3</th>
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
                            <td>{{ $log->objeto }}</td>
                            <td>{{ $log->objeto_id }}</td>
                            <td>{{ $log->name_user }}</td>
                            <td>{{ $log->user->name }}</td>
                            <td>{{ $log->usuario->name }}</td>
                            <td>{{ $log->ip }}</td>
                            <td><a href="{{ route('logs.details', $log->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Código para el botón delete
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
            });

            // Inicializar DataTable
            $('#logs-table').DataTable({
                "pageLength": 10, // cantidad de registros por página
                "lengthMenu": [5, 10, 25, 50, 100],
                "order": [[ 0, "desc" ]], // orden por ID descendente
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });
        });


    </script>
@endsection
