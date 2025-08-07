@extends('layouts.app')

@section('content')
    <h1>Usuarios conectados</h1>

    <table border="1">
        <thead>
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
                <td><a href="{{ route('user_login.details', $user->getId()) }}">Ver</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
