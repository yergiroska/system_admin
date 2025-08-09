@extends('layouts.login')
@section('content')
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" required><br>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required><br>
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
