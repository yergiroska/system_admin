<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Log;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
 *
 * Requisitos y dependencias:
 * - Modelos:
 * Customer, Company, Purchase, Log
 * - Autenticación habilitada para operaciones críticas
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @see      Customer
 * @see      Company
 * @see      Purchase
 * @see      Log
 */
class CustomerController extends Controller
{
    /**
     * Constructor del controlador.
     *
     * Punto de extensión para aplicar middlewares u otras configuraciones
     * a nivel de controlador.
     */
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
     * @throws ValidationException Cuando la validación falla
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
     * @return JsonResponse
     * @throws ValidationException Cuando la validación falla
     */
    public function update($id, Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'identity_document' => 'required|unique:customers,identity_document,' . $id,
        ]);

        $customer = Customer::find($id);
        $customer->setFirstName($request->first_name)
        ->setLastName($request->last_name)
        ->setBirthDate($request->birth_date)
        ->setIdentityDocument($request->identity_document);
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
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
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
    public function viewCustomers(): View
    {
        return view('customers.view_customers');
    }

    /**
     * Devuelve una lista de todos los clientes en formato JSON.
     *
     * Utilizado para peticiones AJAX en la interfaz de usuario.
     *
     * @return JsonResponse
     */
    public function listCustomers(): JsonResponse
    {
        $customers = Customer::all();
        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    public function getProducts(int $id)
    {
        $customer = Customer::find($id);
        $companies = Company::with('products')->get();
        return view('customers.products', [
            'customer' => $customer,
            'companies' => $companies,
        ]);
    }

    /**
     * Registra compras para un cliente específico en la base de datos.
     *
     * Este método realiza las siguientes acciones:
     * 1. Obtiene los IDs de los productos seleccionados en el formulario.
     * 2. Busca al cliente correspondiente al ID proporcionado.
     * 3. Por cada producto seleccionado:
     *    - Crea una nueva instancia de la clase Purchase.
     *    - Asigna el ID del cliente y el ID del producto a la nueva compra.
     *    - Guarda el registro de la compra en la base de datos mediante la relación.
     * 4. Redirige al usuario de vuelta con un mensaje de éxito.
     *
     * @param int $id ID del cliente para el cual se registrarán las compras
     * @param Request $request Contiene los IDs de los productos seleccionados
     * @return RedirectResponse Redirección con un mensaje de estado
     */
    final public function buy(int $id, Request $request): RedirectResponse
    {
        /**
         * 1. Obtener los productos seleccionados del formulario
         */
        $products = $request->input('products');

        $products = collect($products)
            ->filter(fn ($product) => !empty($product['id'])) // descarta nulos o vacíos
            ->map(fn ($product) => (object) $product)
            ->all();

        /**
         * 2. Obtener el cliente correspondiente al ID proporcionado
         */
        $customer = Customer::find($id);
        foreach ($products as $product) {
            /**
             * 1. Crear una nueva instancia de la clase Purchase
             */
            $purchase = new Purchase();
            /**
             * 2. Asignar el ID del cliente y el ID del producto
             */
            // dd($product);

            $purchase->setCompanyProductId($product->id);
            $purchase->setUnitPrice($product->price);
            $purchase->setQuantity($product->quantity);
            $purchase->setTotal($product->total);

            /**
             * 3. Guardar la compra en la base de datos
             * 4. Relacionar la compra con el cliente
             */
            $customer->purchases()->save($purchase);
        }
        return redirect()
            ->back()
            ->with('status', 'Compra registrada correctamente.');
    }

    /**
     * Muestra los detalles de un cliente específico.
     *
     * Este método se encarga de:
     * 1. Obtener el cliente desde la base de datos utilizando su ID
     * 2. Cargar las relaciones necesarias para mostrar información adicional:
     *    - Compras relacionadas con los productos de la empresa y su respectiva compañía
     * 3. Retornar una vista con los datos del cliente y sus relaciones.
     *
     * @param int $id ID del cliente que se desea visualizar
     * @return View Vista con los detalles del cliente
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no se encuentra el cliente con el ID especificado
     */
    public function show($id)
    {
        // Relaciones a precargar para mejorar legibilidad y mantenimiento
        $relations = [
            'purchases.companyProduct.company',
            'purchases.companyProduct.product',
        ];

        $customer = Customer::with($relations)->findOrFail($id);

        return view('customers.show', [
            'customer' => $customer,
        ]);
    }
}
