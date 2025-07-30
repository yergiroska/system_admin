@extends('layouts.app')

@section('content')
    <h1>Crear Empresa</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('companies.store') }}" id="form_company" method="POST">
        @csrf
        <label>Nombre:</label>
        <input type="text" name="name" value="{{ old('name') }}"><br>
        <label>Descripción:</label>
        <textarea id="description" name="description" value="{{ old('description') }}"></textarea><br>
        <button type="submit" id="saved">Guardar</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#saved').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_company').attr('action'),
                    method: $('#form_company').attr('method'),
                    data: $('#form_company').serialize(),
                    success: function(response) {
                        if(response.status === 'success'){
                            alert('Registro exitoso');
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
