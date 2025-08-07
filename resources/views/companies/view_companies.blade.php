@extends('layouts.app')

@section('content')
    <h1>Lista de Empresas</h1>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acción</th>
        </tr>
        </thead>
        <tbody class="companies"></tbody>
    </table>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {

            $.ajax({
                url: '{{ route('companies.lists') }}',
                method: 'GET',
                success: function (response) {
                    let companies = response.data
                    let $tr;
                    for (const company of companies) {
                        $tr += '<tr>';
                        $tr += '<td>'+company.id+'</td>';
                        $tr += '<td>'+company.name+'</td>';
                        $tr += '<td>'+company.description+'</td>';
                        $tr += '<td><a href="' + company.url_detail + '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>';
                        $tr += '</tr>';
                    }
                    $('.companies').append($tr)
                },
                error: function (xhr) {
                    // Manejar errores
                }
            });
        });
    </script>
@endsection
