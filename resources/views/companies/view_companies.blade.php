@extends('layouts.app')

@section('content')
    <div class="container mt-4">
    {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-building"></i> Lista de Empresas</h2>
        </div>
        {{-- Tabla de empresas --}}
        <div class="card shadow">
            <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
                <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acción</th>
                </tr>
                </thead>
                <tbody class="companies"></tbody>
            </table>
        </div>
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
