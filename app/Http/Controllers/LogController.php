<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la gestión de registros (logs) del sistema.
 *
 * Este controlador se encarga de:
 * - Mostrar el listado de todos los registros de actividad del sistema
 * - Permitir ver los detalles específicos de cada registro
 * - Asegurar que solo usuarios autenticados puedan acceder a los logs
 *
 * Los logs registran diferentes tipos de operaciones realizadas en el sistema,
 * como creación, actualización y eliminación de recursos.
 */
class LogController extends Controller
{

    /**
     * Muestra la lista de todos los registros del sistema.
     *
     * Obtiene los registros ordenados por fecha de creación (más recientes primero)
     * y los pasa a la vista para su visualización.
     *
     * @return View Vista con el listado de logs
     */
    public function index()
    {
        $logs = Log::latest()->get();
        return view('logs.index', [
            'logs' => $logs
        ]);
    }

    /**
     * Muestra los detalles específicos de un registro del sistema.
     *
     * Recupera un registro específico por su ID y decodifica los detalles
     * almacenados en formato JSON para mostrarlos en la vista.
     *
     * @param int $id ID del registro a mostrar
     * @return View Vista con los detalles del log
     */
    public function details(int $id)
    {
        $log = Log::find($id);
        $details = json_decode($log->detail);
        return view('logs.details',[
            'log' => $log,
            'details' => $details
        ]);
    }
}
