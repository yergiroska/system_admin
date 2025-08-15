@extends('layouts.app')
@section('content')
    <div class="container mt-5" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                    <i class="fas fa-building"></i> Detalle de la Nota
                </h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>ID:</strong> {{ $note->id }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Título:</strong> {{ $note->title }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Contenido:</strong> {{ $note->contents }}</p>
                </div>
                <div class="mb-3">
                 <p class="mb-1"><strong>Completado:</strong> {{ $note->completed ? 'Sí' : 'No' }}</p>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>Volver a la lista</a>
                </div>
            </div>
        </div>
    </div>
@endsection
