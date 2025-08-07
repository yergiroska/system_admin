@extends('layouts.app')

@section('content')
    <h1>Lista de Empresas</h1>
    <a href="{{ route('companies.create') }}">Crear Empresa</a> |
    <a href="{{ route('companies.view') }}">Lista de Empresas</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($companies as $company)
            <tr id="{!! $company->getId() !!}">
                <td>{{ $company->getName() }}</td>
                <td> {{ $company->getDescription() }}</td>
               <td>
                   <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                   <button data-id="{!! $company->getId() !!}"
                           data-url="{!! route('companies.destroy', $company->getId()) !!}"
                           type="button" class="delete btn btn-sm btn-danger" ><i class="fas fa-trash-alt"></i></button>
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
