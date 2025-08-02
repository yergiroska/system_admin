@extends('layouts.app')

@section('content')
    <h2>Detalles del usuario {!! $user->name !!} </h2> <span> {{ $user->is_connected ? 'Conectado': 'Desconectado' }}</span>
    <table border="1">
        <thead>
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
@endsection

