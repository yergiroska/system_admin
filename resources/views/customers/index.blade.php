@extends('layouts.app')

@section('content')
<h1>Clientes</h1>
<a href="{{ route('customers.create') }}">Crear Cliente</a> |
<a href="{{ route('customers.view') }}">Lista de Clientes</a>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<table>
    <thead>
    <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Fecha de Nacimiento</th>
        <th>DNI</th>
        <th>Acción</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($customers as $customer)
        <tr id="{!! $customer->getId() !!}">
            <td>{{ $customer->getFirstName() }}</td>
            <td>{{ $customer->getLastName()}}</td>
            <td>{{ $customer->getBirthDate() }}</td>
            <td>{{ $customer->getIdentityDocument() }}</td>
            <td>
                <a href="{{ route('customers.edit', $customer) }}">Editar</a>
                <button data-id="{!! $customer->getId() !!}"
                        data-url="{!! route('customers.destroy', $customer->getId()) !!}"
                        type="button"
                        class="delete"
                >Eliminar</button>

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


