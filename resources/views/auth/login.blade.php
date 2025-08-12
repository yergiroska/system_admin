@extends('layouts.login')
@section('content')
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email"
                                   class="form-control"
                                   placeholder="Ingrese su email"
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

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
