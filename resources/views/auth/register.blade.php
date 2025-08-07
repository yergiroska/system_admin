@extends('layouts.login')
@section('content')
<div class="container mt-5" style="max-width: 500px;">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user"></i> Registrar Usuario</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre:</label>
                    <input type="text" name="name" class="form-control" placeholder="Nombre" required><br>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" required><br>
                </div>

                <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required><br>
                </div>

                <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar contraseña:</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar Contraseña" required><br>
                </div>
                <button type="submit" class="btn btn-success">Registrar</button>
            </form>
        </div>
    </div>
</div>
@endsection
