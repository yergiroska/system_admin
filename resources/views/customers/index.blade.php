@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-user"></i> Lista de Clientes</h2>
            <div>
                <a href="{{ route('customers.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear Cliente
                </a>
                <a href="{{ route('customers.view') }}" class="btn btn-info btn-sm text-white">
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

        {{-- Tabla de productos --}}
        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Fecha de Nacimiento</th>
                            <th>DNI</th>
                            <th class="text-center" style="width: 180px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($customers as $customer)
                        <tr id="{!! $customer->getId() !!}">
                            <td>{{ $customer->getFirstName() }}</td>
                            <td>{{ $customer->getLastName()}}</td>
                            <td>{{ $customer->birth_date?->format('d-m-Y') }}</td>
                            <td>{{ $customer->getIdentityDocument() }}</td>
                            <td class="text-center">
                                {{-- Botón editar --}}
                                <a href="{{ route('customers.edit', $customer->getId()) }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Botón Eliminar --}}
                                <button data-id="{!! $customer->getId() !!}"
                                        data-url="{!! route('customers.destroy', $customer->getId()) !!}"
                                        type="button"
                                        class="delete btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                                {{-- Botón comprar --}}
                                <a href="{{ route('customers.get_products', $customer->getId()) }}"
                                   class="btn btn-sm btn-warning text-white" title="Comprar productos">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>

                                {{-- Botón comprar --}}
                                <a href="{{ route('customers.show', $customer->getId()) }}"
                                   class="btn btn-sm btn-info text-white" title="Comprar productos">
                                    <i class="fas fa-eye"></i>
                                </a>

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
                let id = $(this).attr('data-id') // esta y la de abajo hacen lo mismo
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


