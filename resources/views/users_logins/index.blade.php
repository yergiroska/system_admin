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
            <tr id="{{ $user->id }}">
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->is_connected ? 'Si': 'No' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
