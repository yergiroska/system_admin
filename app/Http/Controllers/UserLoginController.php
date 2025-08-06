<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLogin;


/**
 * Controlador para gestionar los registros de inicio de sesión de usuarios.
 *
 * Este controlador se encarga de:
 * - Mostrar el historial de inicios de sesión de un usuario específico
 * - Gestionar la visualización de detalles de acceso al sistema
 * - Vincular la información del usuario con sus registros de inicio de sesión
 *
 * Trabaja en conjunto con los modelos:
 * - UserLogin: Para obtener los registros de inicio de sesión
 * - User: Para obtener la información del usuario
 */
class UserLoginController extends Controller
{

    /**
     * Muestra los detalles de los inicios de sesión de un usuario específico.
     *
     * Este método:
     * - Recupera todos los registros de inicio de sesión asociados al ID del usuario
     * - Obtiene la información del usuario correspondiente
     * - Renderiza la vista con los datos del usuario y su historial de inicios de sesión
     *
     * @param int $id ID del usuario del cual se mostrarán los detalles de inicio de sesión
     * @return \Illuminate\View\View Vista con los detalles del usuario y sus inicios de sesión
     */
    public function details(int $id)
    {
        $users_logins = UserLogin::where('user_id', $id)->get();
        $user = User::find( $id);
        return view('users_logins.details', [
            'user' => $user,
            'users_logins' => $users_logins
        ]);
    }
}
