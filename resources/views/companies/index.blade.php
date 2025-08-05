@extends('layouts.app')

@section('content')
    <h1>Lista de Empresas</h1>
    <a href="{{ route('companies.create') }}">Crear Empresa</a> |
    <a href="{{ route('companies.view') }}">Lista de Empresas</a>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table>
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($companies as $company)
            <tr id="{!! $company->id !!}">
                <td>{{ $company->name }}</td>
                <td> {{ $company->description }}</td>
               <td>
                    <a href="{{ route('companies.edit', $company) }}">Editar</a>
                   <button data-id="{!! $company->id !!}"
                           data-url="{!! route('companies.destroy', $company->id) !!}"
                           type="button" class="delete" >Eliminar</button>
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
