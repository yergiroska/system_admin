<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la gestión de notas en el sistema.
 *
 * Este controlador maneja todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * relacionadas con las notas, incluyendo:
 * - Listado de notas
 * - Creación de nuevas notas
 * - Actualización de notas existentes
 * - Eliminación de notas
 * - Visualización de notas individuales
 *
 * Características principales:
 * - Requiere autenticación para todas las operaciones
 * - Maneja respuestas JSON para operaciones AJAX
 * - Registra logs de actividades críticas (crear y eliminar)
 * - Incluye validación de datos de entrada
 * - Gestiona el estado de completado de las notas
 *
 * El controlador trabaja con el modelo Note y Log para persistencia de datos
 * y registro de actividades respectivamente.
 */
class NoteController extends Controller
{

    /**
     * Muestra la lista de todas las notas.
     *
     * @return View Vista con la lista de todas las notas
     */
    public function index(): View
    {
        $note = Note::all();
        return view('notes.index', [
            'note' => $note
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva nota.
     *
     * @return View Vista del formulario de creación
     */
    public function create(): View
    {
        return view('notes.create');
    }


    /**
     * Almacena una nueva nota en la base de datos y registra la acción en el log.
     *
     * Este método realiza las siguientes operaciones:
     * 1. Valida los datos de entrada del formulario
     * 2. Crea y guarda una nueva nota
     * 3. Registra la acción en el sistema de logs
     * 4. Retorna una respuesta JSON con el resultado
     *
     * @param Request $request Contiene los datos del formulario de creación de la nota
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function store(Request $request): JsonResponse
    {
        // Validación de los campos requeridos del formulario
        $request->validate([
            'title' => 'required',      // Título: campo obligatorio
            'contents' => 'required',    // Contenido: campo obligatorio
            'completed' => 'required',   // Estado de completado: campo obligatorio
        ]);

        // Creación y guardado de la nueva nota
        $note = new Note();

        $note->title = $request->title;         // Establece el título
        $note->contents = $request->contents;   // Establece el contenido
        $note->isNotCompleted(); // Establece el estado inicial de la nota como no completada
        if ($request->completed === '1') {
            $note->isCompleted(); // Marca la nota como completada si se recibe el valor '1'
        }

        $note->save();                           // Guarda la nota en la base de datos

        // Registro de la acción en el sistema de logs
        $log = new Log();
        $log->action = 'CREAR';                  // Tipo de acción realizada
        $log->objeto = 'Notes';  // Entidad afectada
        $log->objeto_id = $note->id;           // ID de la nota creada
        $log->detail = $note->toJson();  // Detalles de la nota en formato JSON
        $log->ip = '3333';   // IP del usuario (valor estático por ahora)
        $log->user_id =auth()->user()->id;  // ID del usuario que creó la nota
        $log->save();                           // Guarda el registro de log

        // Devuelve respuesta JSON con el resultado de la operación
        return response()->json([
            'status' => 'success',
            'message' => 'Nota creada con exito.',
        ]);
    }

    /**
     * Muestra la vista para visualizar notas.
     *
     * @return View Vista para visualización de notas
     */
    public function viewNotes()
    {
        return view('notes.view_notes');
    }

    /**
     * Devuelve una lista de todas las notas en formato JSON.
     *
     * Incluye URL para detalles de cada nota y estado de completado.
     *
     * @return JsonResponse Lista de notas en formato JSON
     */
    public function listNotes(): JsonResponse
    {
        $notas = Note::all();
        $notes = [];
        foreach ($notas as $note) {
            $notes[] = [
                'id' => $note->id,
                'title' => $note->title,
                'contents' => $note->contents,
                'completed' => (bool)$note->completed,
                'url_detail' => route('notes.show', $note->id),
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' =>  $notes,
        ]);
    }

    /**
     * Muestra los detalles de una nota específica.
     *
     * @param int $id ID de la nota a mostrar
     * @return View Vista con los detalles de la nota
     */
    final public function show(int $id): View
    {
        $note = Note::find($id);
        return view('notes.show', [
            'note' => $note
        ]);
    }

    /**
     * Muestra el formulario para editar una nota existente.
     *
     * @param int $id ID de la nota a editar
     * @return View Vista del formulario de edición
     */
    final public function edit(int $id): View
    {
        $note = Note::find($id);
        return view('notes.edit', [
            'note' => $note
        ]);
    }

    /**
     * Actualiza una nota existente en la base de datos.
     *
     * @param int $id ID de la nota a actualizar
     * @param Request $request Datos actualizados de la nota
     * @return JsonResponse Respuesta JSON con el resultado de la operación
     */
    final public function update(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required',
            'contents' => 'required',
            'completed' => 'required',
        ]);

        $note= Note::find($id);
        $note->title = $request->title;
        $note->contents = $request->contents;
        $note->isNotCompleted();
        if ($request->completed === '1') {
            $note->isCompleted();
        }
        $note->save();

        return response()->json([
                'status' => 'success',
                'message' => 'Nota actualizada con exito.',
            ]);

        //return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    /**
     * Elimina una nota del sistema.
     *
     * Registra la eliminación en el log del sistema antes de eliminar la nota.
     *
     * @param int $id ID de la nota a eliminar
     * @return JsonResponse Respuesta JSON con el resultado de la operación
     */
    final public function destroy(int $id): JsonResponse
    {
        $note = Note::find($id);

        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'Notes';
        $log->objeto_id = $note->id;
        $log->detail = $note->toJson();
        $log->ip = '3333';
        $log->user_id = auth()->user()->id;
        $log->save();

        $note->delete();

        return response()->json([
                'status' => 'success',
                'message' => 'Nota eliminada con exito.',
            ]);
    }

}
