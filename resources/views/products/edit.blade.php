@extends('layouts.app')

@section('content')
    <h1>Editar Producto</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product) }}" id="form_product" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}"><br>

        <label>Descripción:</label>
        <input type="text" name="description" value="{{ old('description', $product->description) }}"><br>
        @include('inc.companies')
        <button type="submit" id="update">Actualizar</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#update').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_product').attr('action'),
                    method: $('#form_product').attr('method'),
                    data: $('#form_product').serialize(),
                    success: function(response) {
                        if(response.status === 'success'){
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        // Manejar errores
                    }
                });

            })
        });
    </script>
@endsection
