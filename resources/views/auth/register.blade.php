<h1>Registrar Usuario</h1>
<form action="{{ route('register') }}" method="POST">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="name" placeholder="Nombre" required><br>

    <label>Email:</label>
    <input type="email" name="email" placeholder="Email" required><br>

    <label>Password:</label>
    <input type="password" name="password" placeholder="Password" required><br>

    <label>Nombre:</label>
    <input type="password" name="password_confirmation" placeholder="Confirmar Password" required><br>
    <button type="submit">Registrar</button>
</form>
