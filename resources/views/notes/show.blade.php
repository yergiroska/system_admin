@extends('layouts.app')

@section('content')
    <h1>Detalle de la Nota</h1>

    <p><strong>ID:</strong> {{ $note->id }}</p>
    <p><strong>Título:</strong> {{ $note->title }}</p>
    <p><strong>Contenido:</strong> {{ $note->contents }}</p>
    <p><strong>Completado:</strong> {{ $note->completed ? 'Sí' : 'No' }}</p>

    <a href="{{ url()->previous() }}">Volver a la lista</a>
@endsection
