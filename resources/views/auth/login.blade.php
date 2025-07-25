<h1>Login</h1>
<form action="{{ route('login') }}" method="POST">
    @csrf
    <label>Email:</label>
    <input type="email" name="email" placeholder="Email" required><br>

    <label>Contraseña:</label>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
