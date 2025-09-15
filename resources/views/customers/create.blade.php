@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user"></i> Crear Cliente</h4>
            </div>
            <div class="card-body">
                {{-- Mostrar errores --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('customers.store') }}" id="form_customer" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nombre del Cliente:</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Apellido del Cliente:</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Fecha de Nacimiento:</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="identity_document" class="form-label">DNI:</label>
                        <input type="text" name="identity_document" class="form-control" value="{{ old('identity_document') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen del Cliente:</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
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

                const form = document.getElementById('form_customer');
                const formData = new FormData(form);

                $.ajax({
                    url: $('#form_customer').attr('action'),
                    method: $('#form_customer').attr('method'),
                    data: formData,
                    processData: false,   // Importante para FormData
                    contentType: false,   // Importante para FormData
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
