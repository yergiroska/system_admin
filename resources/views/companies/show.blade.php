@extends('layouts.app')

@section('content')
    <h1>Detalle de la Empresa</h1>

    <p><strong>ID:</strong> {{ $company->id }}</p>
    <p><strong>Nombre:</strong> {{ $company->name }}</p>
    <p><strong>Descripción:</strong> {{ $company->description }}</p>

    <a href="{{ url()->previous() }}">Volver a la lista</a>
@endsection
