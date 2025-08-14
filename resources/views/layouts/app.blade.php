<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
{{-- Incluimos el menú (navbar) que ahora mostrará usuario y reloj --}}
@include('components.menu')
<div class="container mt-4">
    @yield('content')
</div>
<hr>
<footer class="text-center py-3">
    <p>&copy; {{ date('Y') }} - Sistema de Administración </p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
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
@yield('scripts')
</body>
</html>
