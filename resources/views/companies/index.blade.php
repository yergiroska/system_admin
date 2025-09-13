@extends('layouts.app')
@section('content')
    <div class="container mt-4">
    {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-building"></i> Lista de Empresas</h2>
            <div>
                <a href="{{ route('companies.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear Empresa
                </a>
                <a href="{{ route('companies.view') }}" class="btn btn-info btn-sm text-white">
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
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Imagen de la Empresa</th>
                        <th class="text-center" style="width: 180px;">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($companies as $company)
                        <tr id="{!! $company->getId() !!}">
                            <td>{{ $company->getName() }}</td>
                            <td> {{ $company->getDescription() }}</td>
                            <td>
                                <img src="{{ $company->image_url ?? '' }}" alt="{{ $company->name }}"
                                     style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                            </td>
                            <td class="text-center">
                               <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-primary" title="Editar">
                                   <i class="fas fa-edit"></i>
                               </a>
                               <button data-id="{!! $company->getId() !!}"
                                       data-url="{!! route('companies.destroy', $company->getId()) !!}"
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
