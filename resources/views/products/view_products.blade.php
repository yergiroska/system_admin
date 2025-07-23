@extends('layouts.app')

@section('content')
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody class="products"></tbody>
    </table>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {

            $.ajax({
                url: '{{ route('products.lists') }}',
                method: 'GET',
                success: function (response) {
                    let products = response.data
                    let $tr;
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
