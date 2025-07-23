<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Administración</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<nav>
 @include('components.menu')
</nav>

<hr>

@yield('content')

<hr>
<footer>
    <p>&copy; {{ date('Y') }} - Laravel App</p>
</footer>
@yield('scripts')
</body>
</html>
