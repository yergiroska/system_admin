<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


/**
 * Controlador para la gestión de usuarios en el sistema.
 *
 * Este controlador maneja las operaciones relacionadas con los usuarios,
 * incluyendo:
 * - Listado de usuarios
 *
 * Características:
 * - Obtiene y muestra la lista de usuarios ordenados por fecha de creación
 * - Renderiza la vista correspondiente con los datos de usuarios
 * - Utiliza el modelo User para interactuar con la base de datos
 */
class UserController extends Controller
{
    /**
     * Muestra la lista de todos los usuarios.
     *
     * Este método:
     * - Obtiene todos los usuarios ordenados por fecha de creación (más recientes primero)
     * - Pasa los usuarios a la vista para su visualización
     *
     * @return \Illuminate\View\View Vista con la lista de usuarios
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', [
            'users' => $users
        ]);
    }
}
