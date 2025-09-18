<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

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
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales son incorrectas'
            ], 401);
        }

        // Si las credenciales son correctas, retornar los datos del usuario
        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    /**
     * Manejar el logout del usuario
     */
    public function logout(Request $request)
    {
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
