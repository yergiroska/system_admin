@extends('layouts.app')

@section('content')
    <h1>Producto</h1>
    <a href="{{ route('products.create') }}">Crear Producto</a> |
    <a href="{{ route('products.view.products') }}">Lista de Productos</a>

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
        @foreach ($products as $product)
            <tr id="{!! $product->id !!}">
                <td>{{ $product->name }}</td>
                <td> {{ $product->description }}</td>
                <td>
                    <a href="{{ route('products.edit', $product) }}">Editar</a>
                    <button data-id="{!! $product->id !!}"
                            data-url="{!! route('products.destroy', $product->id) !!}"
                            type="button" id="delete" >Eliminar</button>
                </td>
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


