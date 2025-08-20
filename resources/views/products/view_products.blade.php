@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-building"></i> Lista de Productos</h2>
        </div>
        {{-- Tabla de empresas --}}
        <div class="card shadow">
        <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody class="products"></tbody>
        </table>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('products.lists') }}',
                method: 'GET',
                success: function (response) {
                    let products = response.data
                    let $tr = '';
                    for (const product of products) {
                        $tr += '<tr>';
                        $tr += '<td>'+product.id+'</td>';
                        $tr += '<td>'+product.name+'</td>';
                        $tr += '<td>'+product.description+'</td>';
                        $tr += '</tr>';
                    }
                    $('.products').append($tr)
                },
                error: function (xhr) {
                    // Manejar errores
                }
            });
        });
    </script>
@endsection
