@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">
                <i class="fas fa-users"></i> Lista de Clientes
            </h2>
        </div>

        {{-- Tabla de clientes --}}
        <div class="card shadow">
                <table class="table table-striped table-hover table-bordered mb-0 table-responsive">
            <thead class="table-primary">
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
        </div>
    </div>
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
                        let fecha = 'No tiene datos'
                        if(customer.birth_date_format !== null){
                            fecha = customer.birth_date_format
                        }
                        $tr += '<tr>';
                        $tr += '<td>'+customer.id+'</td>';
                        $tr += '<td>'+customer.first_name+'</td>';
                        $tr += '<td>'+customer.last_name+'</td>';
                        $tr += '<td>'+fecha+'</td>';
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
