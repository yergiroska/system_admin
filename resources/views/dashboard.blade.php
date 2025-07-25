@extends('layouts.app')

@section('content')
    <h1>Bienvenido, {{ Auth::user()->name }}</h1>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <!--<button type="submit">Cerrar sesión</button>-->
    </form>
@endsection
