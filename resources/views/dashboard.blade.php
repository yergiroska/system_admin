@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card border-0">
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">
                    <i class="fas fa-tachometer-alt"></i> Panel de Control
                </h2>
            </div>
            <div class="card-body text-center">
                <h4 class="mb-3">
                    Bienvenido, <strong>{{ Auth::user()->name }}</strong>
                </h4>
                <p class="text-muted">Has iniciado sesión correctamente.</p>

                <div class="mt-4">
                    <a href="{{ route('companies.index') }}" class="btn btn-success me-2">
                        <i class="fas fa-building"></i> Ver Empresas
                    </a>
                    <a href="{{ route('notes.index') }}" class="btn btn-info text-white me-2">
                        <i class="fas fa-sticky-note"></i> Ver Notas
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

