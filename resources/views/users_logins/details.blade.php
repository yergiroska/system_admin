@extends('layouts.app')

@section('content')
    <div class="container mt-4">
            {{-- Encabezado --}}
            <div class="card-header text-dark">
                <h2 class="mb-0"><i class="fas fa-file-alt"></i>Detalles del usuario: {!! $user->name !!} </h2>
                <span class="text-dark"> {{ $user->is_connected ? 'Conectado': 'Desconectado' }}</span>
            </div>

            <div class="card shadow">
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0 table-responsive table-bordered">
                        <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Inicio de Session</th>
                            <th>Fin de Session</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users_logins as $key =>$user_login)
                            <tr>
                                <td>{!! $user_login->id !!}</td>
                                <td>{!! $user_login->start_connection ? \Carbon\Carbon::parse($user_login->start_connection)->format('d/m/Y H:i:s'): ''  !!}</td>
                                <td>{!! $user_login->end_connection ? \Carbon\Carbon::parse($user_login->end_connection)->format('d/m/Y H:i:s') : ''  !!}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
@endsection

