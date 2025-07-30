@extends('layouts.app')

@section('content')
    <h1>Editar Empresa</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('companies.update', $company) }}" id="form_company" method="POST">
        @csrf
        @method('PUT')

        <label>Nombre:</label>
        <input type="text" name="name" value="{{ old('name', $company->name) }}"><br>

        <label>Descripción:</label>
        <textarea name="description">{{ old('description', $company->description) }}</textarea><br>
        <button type="submit" id="update">Actualizar</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#update').on('click', function(evento) {
                evento.preventDefault();
                $.ajax({
                    url: $('#form_company').attr('action'),
                    method: $('#form_company').attr('method'),
                    data: $('#form_company').serialize(),
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
