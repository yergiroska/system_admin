<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la gestión de clientes en el sistema.
 *
 * Este controlador maneja todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * relacionadas con los clientes, incluyendo:
 * - Listado de clientes
 * - Creación de nuevos clientes
 * - Actualización de información de clientes
 * - Eliminación de clientes
 *
 * También se encarga de:
 * - Validación de datos de entrada
 * - Registro de logs para operaciones críticas (crear, eliminar)
 * - Manejo de respuestas JSON para operaciones AJAX
 * - Verificación de autenticación de usuarios
 */
class CustomerController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Muestra la lista de todos los clientes.
     *
     * Si el usuario no está autenticado, será redirigido a la página de login.
     *
     * @return View|RedirectResponse
     */
    public function index()
    {
        if(!auth()->user()){
            return redirect()->route('login');
        }
        $customers = Customer::all();
        return view('customers.index', [
            'customers' => $customers
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo cliente.
     *
     * @return View
     */
    public function create()
    {
        return view('customers.create');
    }


    /**
     * Almacena un nuevo cliente en la base de datos.
     *
     * Este método se encarga de:
     * 1. Validar los datos de entrada del formulario
     * 2. Crear un nuevo registro de cliente
     * 3. Registrar la acción en el log del sistema
     * 4. Devolver una respuesta JSON con el resultado
     *
     * @param Request $request Contiene los datos del formulario de creación del cliente
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    public function store(Request $request)
    {
        // Validación de los campos requeridos del formulario
        $request->validate([
            'first_name' => 'required',          // Nombre: campo obligatorio
            'last_name' => 'required',           // Apellido: campo obligatorio
            'birth_date' => 'required|date',     // Fecha de nacimiento: obligatorio y debe ser una fecha válida
            'identity_document' => 'required|unique:customers', // Documento de identidad: obligatorio y único en la tabla customers
        ]);

        // Creación de una nueva instancia del modelo Customer y asignación de valores
        $customer = new Customer();
        $customer->setFirstName($request->first_name);
        $customer->setLastName($request->last_name);
        $customer->setBirthDate($request->birth_date);
        $customer->setIdentityDocument($request->identity_document);
        $customer->save(); // Guarda el nuevo cliente en la base de datos

        // Registro de la acción en el sistema de logs
        $log = new Log();
        $log->setAction('CREAR');                  // Tipo de acción realizada
        $log->setObjeto('customers');              // Tabla afectada
        $log->setObjetoId($customer->id);        // ID del registro creado
        $log->setDetail($customer->toJson());      // Detalles del cliente en formato JSON
        $log->setIp('1111');                      // IP del usuario (pendiente implementación real)
        $log->setUserId(auth()->user()->id);      // ID del usuario que realizó la acción
        $log->save();                           // Guarda el registro de log

        // Devuelve respuesta JSON con el resultado de la operación
        return response()->json([
           'status' => 'success',
           //'data' => $customer,
           'message' => 'Cliente creado con exito',
       ]);

        //return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Muestra el formulario para editar un cliente existente.
     *
     * @param int $id ID del cliente a editar
     * @return View
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    /**
     * Actualiza la información de un cliente existente.
     *
     * @param int $id ID del cliente a actualizar
     * @param Request $request Los datos actualizados del cliente
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'identity_document' => 'required|unique:customers,identity_document,' . $id,
        ]);

        $customer = Customer::find($id);
        $customer->setFirstName($request->first_name);
        $customer->setLastName($request->last_name);
        $customer->setBirthDate($request->birth_date);
        $customer->setIdentityDocument($request->identity_document);
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente actualizado con exito',
        ]);

        //return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Elimina un cliente del sistema.
     *
     * Registra la eliminación en el log del sistema antes de eliminar el cliente.
     *
     * @param int $id ID del cliente a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        $log = new Log();
        $log->setAction('ELIMINAR');
        $log->setObjeto('customers');
        $log->setObjetoId($id);
        $log->setDetail($customer->toJson());
        $log->setIp('1111');
        $log->setUserId(auth()->user()->id);
        $log->save();

        $customer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente eliminado con exito',
        ]);

        //return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Muestra la vista para visualizar clientes.
     *
     * @return View
     */
    public function viewCustomers()
    {
        return view('customers.view_customers');
    }

    /**
     * Devuelve una lista de todos los clientes en formato JSON.
     *
     * Utilizado para peticiones AJAX en la interfaz de usuario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listCustomers(): JsonResponse
    {
        $customers = Customer::all();
        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

}
