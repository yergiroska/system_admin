<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i> Panel
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('companies.index') }}">
                        <i class="fas fa-building"></i> Empresas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">
                        <i class="fas fa-box"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('customers.index') }}">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logs.index') }}">
                        <i class="fas fa-clipboard-list"></i> Logs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <i class="fas fa-user-friends"></i> Usuarios Conectados
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('notes.index') }}">
                        <i class="fas fa-sticky-note"></i> Notas
                    </a>
                </li>
            </ul>

            {{-- Usuario logueado y logout, derecha: hora y usuario --}}
            <div class="d-flex align-items-center">
                {{-- Reloj: server time provisto aquí como atributo data --}}
                <div class="text-light me-3 text-end">
                    <div style="font-size:0.9rem;">
                        Fecha - Hora:
                        <strong>
                            <small id="current-time">
                                {{ \Carbon\Carbon::now('Europe/Madrid')->format('d-m-Y H:i') }}
                            </small>
                        </strong>
                    </div>
                </div>
            @auth
                {{--<span class="navbar-text text-white me-3">
                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                </span>--}}
                <div class="me-3 text-light text-end">
                    <div style="font-size:0.9rem;">
                        <i class="fas fa-user"></i> {{ Auth::user()->name }}
                    </div>
                    @if(session('last_logout_time'))
                        <div class="text-muted" style="font-size:0.8rem;">
                            Última sesión: {{ session('last_logout_time') }}
                        </div>
                    @endif
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </button>
                </form>
            @endauth
            </div>
        </div>
    </div>
</nav>
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




















{{--<a href="{{ route('companies.index') }}">Empresas</a> |
<a href="{{ route('products.index') }}">Productos</a> |
<a href="{{ route('customers.index') }}">Clientes</a> |
<a href="{{ route('logs.index') }}">Logs</a> |
<a href="{{ route('users.index') }}">Usuarios Conectados</a> |
<a href="{{ route('notes.index') }}">Notas</a>--}}



