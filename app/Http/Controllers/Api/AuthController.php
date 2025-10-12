<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Manejar el login del usuario
     */
    public function login(Request $request)
    {
        // Validar los datos que llegan del frontend
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Buscar al usuario por email
        $user = User::with('customer')->where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales son incorrectas'
            ], 401);
        }

        // Generar y guardar el api_token
        $user->api_token = Str::random(60);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'customer' => $user->customer,
            ],
            'api_token' => $user->api_token,
        ], 200);
    }

    /**
     * Manejar el logout del usuario
     */
    public function logout(Request $request)
    {
        // Limpiar la sesión
        session()->forget(['user_authenticated', 'user_id']);
        session()->invalidate();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ], 200);
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function me(Request $request)
    {
        // Por ahora, esto es básico. Más adelante podemos mejorarlo
        return response()->json([
            'success' => true,
            'message' => 'Usuario autenticado'
        ], 200);
    }
}
