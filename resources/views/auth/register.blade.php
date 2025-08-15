@extends('layouts.login')
@section('content')
<div class="container mt-5" style="max-width: 500px;">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-plus"></i> Registrar Usuario</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre de Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" id="name"
                               class="form-control"
                               placeholder="Ingrese su nombre de usuario"
                               value="{{ old('name') }}"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email"
                               class="form-control"
                               placeholder="Ingrese su correo electrónico"
                               value="{{ old('email') }}"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password"
                               class="form-control"
                               placeholder="Ingrese su contraseña"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control"
                               placeholder="Repita su contraseña"
                               required>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Registrar
                    </button>
                </div>
                <div class="d-grid mt-4 text-center">
                    <a href="{{ route('login') }}" >
                        Iniciar sesión
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
