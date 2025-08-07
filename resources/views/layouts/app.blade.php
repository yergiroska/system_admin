<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Administración</title>
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
@if(Auth::check())
    <div class="text-end">
        Logueado como: <strong>{{ Auth::user()->name }}</strong>
       <div>
        @if(session('last_logout_time'))
            Última sesión: <small>{{ session('last_logout_time') }}</small>
        @endif
       </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Cerrar Sesión</button>
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
</div>
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

{{-- Bootstrap JS Bundle con Popper --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
