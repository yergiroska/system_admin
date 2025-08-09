@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
              <h4 class="mb-0"><i class="fas fa-building"></i> Crear Empresa</h4>
            </div>
            <div class="card-body">

            {{-- Mostrar errores --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

                    {{-- Formulario --}}
                <form action="{{ route('companies.store') }}" id="form_company" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre:</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"><br>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción:</label>
                        <textarea id="description" name="description" class="form-control" rows="3" value="{{ old('description') }}"></textarea><br>
                    </div>

                    {{-- Productos asociados --}}
                    <div class="mb-3">
                        @include('inc.products')
                    </div>
                    <div class="d-grid">
                        <button type="submit" id="saved" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
