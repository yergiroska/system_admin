@extends('layouts.login')
@section('content')
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h4>
            </div>
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <label>Email:</label>
                <input type="email" name="email" placeholder="Email" required><br>

                <label>Contraseña:</label>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
@endsection
