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
       <div>
        @if(session('last_logout_time'))
            Última sesión: <small>{{ session('last_logout_time') }}</small>
        @endif
       </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit">Cerrar Sesión</button>
        </form>
    </div>
@endif
<nav>
    <div>
        Fecha - Hora Actual: <small id="current-time">{{ \Carbon\Carbon::now('Europe/Madrid')->format('d-m-Y H:i') }}</small>
    </div>
 @include('components.menu')
</nav>

@yield('content')

<hr>
<footer>
    <p>&copy; {{ date('Y') }} - Sistema de Administración </p>
</footer>
@yield('scripts')
<script>
    function updateTime() {
        const now = new Date();

        // Formatear la fecha como dd-mm-yyyy hh:mm
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        document.getElementById('current-time').textContent = `${day}-${month}-${year} ${hours}:${minutes}`;
    }

    // Actualizar inmediatamente y luego cada minuto
    updateTime();
    setInterval(updateTime, 60000); // 60000 ms = 1 minuto
</script>
</body>
</html>
