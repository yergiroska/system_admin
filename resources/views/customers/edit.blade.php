@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-edit"></i> Editar Cliente
                </h4>
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

                <form action="{{ route('customers.update', $customer) }}" id="form_customer" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nombre:</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $customer->first_name }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Apellido:</label>
                        <input type="text" name="last_name" class="form-control" value="{{  $customer->last_name }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="birth_date" class="form-label">Fecha de Nacimiento:</label>
                        <input type="date" name="birth_date" class="form-control" value="{{  $customer->birth_date?->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="identity_document" class="form-label">DNI:</label>
                        <input type="text" name="identity_document" class="form-control" value="{{  $customer->identity_document }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen del Cliente:</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="update" class="btn btn-success text-white">
                            <i class="fas fa-save"></i> Actualizar
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
            $('#update').on('click', function(evento) {
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
