@extends('layouts.app')

@section('content')
    <h1>Lista de Clientes</h1>
    <table border="1">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Fecha de Nacimiento</th>
            <th>DNI</th>
        </tr>
        </thead>
        <tbody class="customers"></tbody>
    </table>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {

            $.ajax({
                url: '{{ route('customers.lists') }}',
                method: 'GET',
                success: function (response) {
                    let customers = response.data
                    let $tr='';
                    for (const customer of customers) {
                        $tr += '<tr>';
                        $tr += '<td>'+customer.id+'</td>';
                        $tr += '<td>'+customer.first_name+'</td>';
                        $tr += '<td>'+customer.last_name+'</td>';
                        $tr += '<td>'+customer.formatted_birth_date+'</td>';
                        $tr += '<td>'+customer.identity_document+'</td>';
                        $tr += '</tr>';
                    }
                    $('.customers').append($tr)
                },
                error: function (xhr) {
                    // Manejar errores
                }
            });
        });
    </script>
@endsection
