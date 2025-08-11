@extends('layouts.app')

@section('content')
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Producto</h4>
            </div>
            <div class="card-body">
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
                <form action="{{ route('products.update', $product) }}" id="form_product" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre:</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}"><br>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción:</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea><br>

                    </div>
                    <div class="mb-3">
                        @include('inc.companies')
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
