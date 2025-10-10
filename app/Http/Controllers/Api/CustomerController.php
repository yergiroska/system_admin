<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Log;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
     * Muestra la lista de todos los clientes.
     *
     * Si el usuario no está autenticado, será redirigido a la página de login.
     *
     * @return View|RedirectResponse
     */
    public function index()
    {
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
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->setBirthDate($request->birth_date);
        $customer->identity_document =$request->identity_document;
        // Subida de imagen (opcional)
        if ($request->hasFile('image')) {
            // Guardar en storage/app/public/
            $filename = $request->file('image')->hashName();
            $content_image = file_get_contents($request->file('image'));
            Storage::disk('public')->put('images/' . $filename, $content_image);
            $customer->image_url = $filename;

        }
        $customer->save(); // Guarda el nuevo cliente en la base de datos

        // Registro de la acción en el sistema de logs
        $user = User::first();

        $userId = auth()->user() ? auth()->user()->id : $user->id;
        $log = new Log();
        $log->action = 'CREAR';                  // Tipo de acción realizada
        $log->objeto = 'customers';              // Tabla afectada
        $log->objeto_id =$customer->id;        // ID del registro creado
        $log->detail = $customer->toJson();      // Detalles del cliente en formato JSON
        $log->ip = '1111';                      // IP del usuario (pendiente implementación real)
        $log->user_id = $userId;      // ID del usuario que realizó la acción
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
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->setBirthDate($request->birth_date);
        $customer->identity_document = $request->identity_document;
        // Si se sube una nueva imagen, opcionalmente elimina la anterior y actualiza
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($customer->image_url) {
                Storage::disk('public')->delete('images/'.$customer->image_url);
            }

            $filename = $request->file('image')->hashName();
            $content_image = file_get_contents($request->file('image'));
            Storage::disk('public')->put('images/' . $filename, $content_image);
            $customer->image_url = $filename;
        }
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
        if ($customer->image_name) {
            Storage::disk('public')->delete('images/'.$customer->image_name);
        }

        // Registro de la eliminación en el log
        $user = User::first();
        $userId = auth()->user() ? auth()->user()->id : $user->id;
        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'customers';
        $log->objeto_id = $id;
        $log->detail = $customer->toJson();
        $log->ip = '1111';
        $log->user_id = $userId;
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
     * @param int $id ID del cliente para el cual se registrarán las compras
     * @param Request $request Contiene los productos seleccionados para comprar
     * @return JsonResponse Respuesta JSON con el estado de la operación
     */
    final public function buy(int $id, Request $request): JsonResponse
    {
        try {
            /**
             * 1. Validar entrada
             */
            $request->validate([
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|integer',
                'products.*.quantity' => 'required|integer|min:1',
                'products.*.price' => 'required|numeric|min:0',
                'products.*.total' => 'required|numeric|min:0',
            ], [
                'products.required' => 'Debe seleccionar al menos un producto',
                'products.*.quantity.min' => 'La cantidad debe ser mayor a 0',
            ]);

            /**
             * 2. Obtener el cliente
             */
            $customer = Customer::findOrFail($id);

            /**
             * 3. Procesar productos
             */
            $products = collect($request->input('products'))
                ->filter(fn($product) => !empty($product['id']) && $product['quantity'] > 0)
                ->map(fn($product) => (object) $product);

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay productos válidos para procesar'
                ], 400);
            }

            /**
             * 4. Registrar compras en transacción
             */
            $purchasesSaved = [];
            $totalAmount = 0;

            \DB::transaction(function () use ($customer, $products, &$purchasesSaved, &$totalAmount) {
                foreach ($products as $product) {
                    $purchase = new Purchase();
                    $purchase->company_product_id = $product->id;
                    $purchase->unit_price = $product->price;
                    $purchase->quantity = $product->quantity;
                    $purchase->total = $product->total;

                    $customer->purchases()->save($purchase);

                    $purchasesSaved[] = [
                        'id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $product->quantity,
                        'unit_price' => $product->price,
                        'total' => $product->total,
                    ];

                    $totalAmount += $product->total;
                }
            });

            /**
             * 5. Registrar en logs
             */
            //$this->logPurchase($customer, $purchasesSaved, $totalAmount);

            /**
             * 6. Respuesta exitosa
             */
            return response()->json([
                'success' => true,
                'message' => 'Compra registrada correctamente',
                'data' => [
                    'purchase_id' => uniqid('PUR_'),
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->first_name . ' ' . $customer->last_name,
                        'document' => $customer->identity_document,
                    ],
                    'user' => [
                        'id' => auth()->user()?->id,
                        'name' => auth()->user()?->name,
                        'email' => auth()->user()?->email,
                    ],
                    'summary' => [
                        'total_items' => count($purchasesSaved),
                        'total_amount' => $totalAmount,
                        'currency' => 'EUR',
                    ],
                    'purchases' => $purchasesSaved,
                    'timestamp' => now()->toISOString(),
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error en compra de cliente', [
                'customer_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Contacte al administrador.'
            ], 500);
        }
    }

    /**
     * Registra la compra en el sistema de logs
     */
    /*private function logPurchase(Customer $customer, array $purchases, float $totalAmount): void
    {
        try {
            $user = User::first();
            $userId = auth()->user()?->id ?? $user->id;

            $log = new Log();
            $log->action = 'COMPRA';
            $log->objeto = 'purchases';
            $log->objeto_id = $customer->id;
            $log->detail = json_encode([
                'customer_id' => $customer->id,
                'customer_name' => $customer->first_name . ' ' . $customer->last_name,
                'user_id' => auth()->user()?->id,
                'user_name' => auth()->user()?->name,
                'total_amount' => $totalAmount,
                'total_items' => count($purchases),
                'purchases' => $purchases
            ]);
            $log->ip = request()->ip();
            $log->user_id = $userId;
            $log->save();

        } catch (\Exception $e) {
            \Log::warning('Error al registrar log de compra', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
        }
    }*/

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

    // Usuario autenticado
    /**
     * Obtiene la información del cliente asociado al usuario autenticado
     *
     * @param Request $request
     * @return JsonResponse
     */
    final public function getAuthenticatedCustomer(Request $request): JsonResponse
    {
        try {
            $user = $request->user(); // Usuario autenticado
            //$user = Auth::user(); // o este

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $customer = $user->customer; // Relación user->customer

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->first_name . ' ' . $customer->last_name, // ✅ Corregido
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuario: ' . $e->getMessage()
            ], 500);
        }
    }
}
