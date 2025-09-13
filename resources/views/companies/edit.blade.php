@extends('layouts.app')
@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Empresa</h4>
            </div>
            <div class="card-body">
                {{-- Errores --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulario --}}
                <form action="{{ route('companies.update', $company) }}" id="form_company" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre de la Empresa:</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}"><br>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción:</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $company->description) }}</textarea><br>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Imagen de la Empresa:</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>

                    {{-- Productos asociados --}}
                    <div class="mb-3">
                        @include('inc.products')
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
                const form = document.getElementById('form_company');
                const formData = new FormData(form);

                $.ajax({
                    url: $('#form_company').attr('action'),
                    method: $('#form_company').attr('method'),
                    data: formData,
                    processData: false,   // Importante para FormData
                    contentType: false,   // Importante para FormData
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
