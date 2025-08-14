@extends('layouts.app')
@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-list"></i> Lista de Notas</h2>
            <div>
                <a href="{{ route('notes.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear Nota
                </a>
                <a href="{{ route('notes.view') }}" class="btn btn-info btn-sm text-white">
                    <i class="fas fa-list"></i> Lista Detallada
                </a>
            </div>
        </div>

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Tabla de empresas --}}
        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
                    <thead class="table-primary">
                    <tr>
                        <th>Título</th>
                        <th>Contenido</th>
                        <th>Completado</th>
                        <th class="text-center" style="width: 180px;">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($note as $note)
                        <tr id="{!! $note->getId() !!}">
                            <td>{{ $note->getTitle() }}</td>
                            <td>{{ $note->getContents() }}</td>
                            <td>{{ $note->getCompleted() ? 'Si' : 'No' }}</td>
                            <td class="text-center">
                                <a href="{{ route('notes.edit', $note) }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button data-id="{!! $note->getId() !!}"
                                        data-url="{!! route('notes.destroy', $note->getId()) !!}"
                                        type="button" class="delete btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
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

