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
                    <th>Imagen del Producto</th>
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
                        let imageSrc = product.image_url ? '/storage/images/' + product.image_url : 'https://via.placeholder.com/64x64?text=No+Img';

                        $tr += '<tr>';
                        $tr += '<td>'+product.id+'</td>';
                        $tr += '<td>'+product.name+'</td>';
                        $tr += '<td>'+product.description+'</td>';
                        $tr += '<td>';
                        $tr += '<img src="' + imageSrc + '" alt="' + product.name + '" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">';
                        $tr += '</td>';
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
