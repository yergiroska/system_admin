<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Administración</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
@if(Auth::check())
    <div style="position: absolute; top: 10px; right: 10px;">
        Logueado como: <strong>{{ Auth::user()->name }}</strong>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit">Cerrar Sesión</button>
        </form>
    </div>
@endif
<nav>
 @include('components.menu')
</nav>

<hr>

@yield('content')

<hr>
<footer>
    <p>&copy; {{ date('Y') }} - Sistema de Administración </p>
</footer>
@yield('scripts')
</body>
</html>
