@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0"><i class="fas fa-users"></i> Usuarios conectados</h2>
        </div>

        {{-- Tabla de empresas --}}
        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
                    <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Esta conectado</th>
                        <th>Ver</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr id="{{ $user->getId() }}">
                            <td>{{ $user->getId() }}</td>
                            <td>{{ $user->getName() }}</td>
                            <td>{{ $user->getIsConnected() ? 'Si': 'No' }}</td>
                            <td><a href="{{ route('user_login.details', $user->getId()) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
