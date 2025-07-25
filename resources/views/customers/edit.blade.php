@extends('layouts.app')

@section('content')
    <h1>Editar Cliente</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.update', $customer) }}" id="form_customer" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}"><br>

        <label>Apellido:</label>
        <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}"><br>

        <label>Fecha de Nacimiento:</label>
        <input type="date" name="birth_date" value="{{ old('birth_date', $customer->birth_date) }}"><br>

        <label>DNI:</label>
        <input type="text" name="identity_document" value="{{ old('identity_document', $customer->identity_document) }}"><br>

        <button type="submit" id="update">Actualizar</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#update').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_customer').attr('action'),
                    method: $('#form_customer').attr('method'),
                    data: $('#form_customer').serialize(),
                    success: function(response) {
                        if(response.status === 'success'){
                            alert('Actualizado con exito');
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
