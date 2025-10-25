<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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


    /**
     * Registrar nuevo usuario
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validar datos
            $validated = $request->validate([
                'name' => 'required|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed'
            ]);

            \DB::beginTransaction();

            // Crear usuario
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            // Actualiza la última sesión al momento actual en la zona horaria de Madrid
            $user->setLastSession();

            // Marca al usuario como conectado
            $user->setConnected();

            // Generar api_token (igual que en login)
            $user->api_token = Str::random(60);

            // Guarda los cambios en la base de datos
            $user->save();

            // Crear customer asociado
            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->save();

            // Crear registro de inicio de sesión
            $user_login = new UserLogin();
            $user_login->user_id = $user->id;
            $user_login->setStartConnection();
            $user_login->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'api_token' => $user->api_token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de registro inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se pudo completar el registro. Inténtalo de nuevo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
