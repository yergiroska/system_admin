@extends('layouts.app')

@section('content')
    <h1>Create Product</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" id="form_product" method="POST">
        @csrf

        <label>Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}"><br>

        <label>Description:</label>
        <input type="text" id="description" name="description" value="{{ old('description') }}"><br>

        <button type="submit" id="saved">Save</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#saved').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_product').attr('action'),
                    method: $('#form_product').attr('method'),
                    data: $('#form_product').serialize(),
                    success: function(response) {
                        if(response.status === 'success'){
                            alert('Registro existoso');
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
