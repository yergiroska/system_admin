<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Log;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controlador para la gestión de empresas en el sistema.
 *
 * Este controlador maneja todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * relacionadas con las empresas, incluyendo:
 * - Listado de empresas
 * - Creación de nuevas empresas
 * - Actualización de información de empresas
 * - Eliminación de empresas
 * - Gestión de relaciones con productos
 *
 * También se encarga de:
 * - Validación de datos de entrada
 * - Registro de logs para operaciones críticas
 * - Manejo de respuestas JSON para operaciones AJAX
 * - Verificación de autenticación mediante middleware
 */
class CompanyController extends Controller
{
    /**
     * Constructor del controlador.
     * Aplica el middleware de autenticación a todas las acciones del controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la lista de todas las empresas.
     *
     * @return \Illuminate\View\View Vista con la lista de empresas
     */
    public function index()
    {
        $companies = Company::all();
        return view('companies.index', [
            'companies' => $companies
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva empresa.
     * Incluye la lista de todos los productos disponibles para asociar.
     *
     * @return \Illuminate\View\View Vista del formulario de creación
     */
    public function create()
    {
        $products = Product::all();
        return view('companies.create', [
            'products' => $products,
        ]);
    }

    /**
     * Almacena una nueva empresa en la base de datos.
     *
     * Este método realiza las siguientes operaciones:
     * 1. Valida los datos de entrada del formulario
     * 2. Crea una nueva empresa
     * 3. Asocia los productos seleccionados
     * 4. Registra la operación en el log del sistema
     * 5. Retorna respuesta JSON con el resultado
     *
     * @param Request $request Contiene los datos del formulario de creación
     * @return JsonResponse Respuesta con el estado de la operación
     */
    public function store(Request $request)
    {
        // Validación de campos requeridos
        $request->validate([
            'name' => 'required',         // Nombre de la empresa: campo obligatorio
            'description' => 'required',   // Descripción de la empresa: campo obligatorio
        ]);

        // Creación de nueva instancia de Company y asignación de valores
        $company = new Company();
        $company->setName($request->name);         // Asigna el nombre de la empresa
        $company->setDescription($request->description); // Asigna la descripción
        $company->save();   // Guarda la empresa en la base de datos

        // Asocia los productos seleccionados a la empresa (si hay alguno)
        $company->products()->attach($request->products ?? []);

        // Registro de la acción en el sistema de logs
        $log = new Log();
        $log->action = 'CREAR';                  // Tipo de acción realizada
        $log->objeto = 'Empresas';              // Entidad afectada
        $log->objeto_id = $company->id;        // ID del registro creado
        $log->detail = $company->toJson();      // Detalles de la empresa en formato JSON
        $log->ip = '4444';                     // IP del usuario (valor estático)
        $log->user_id = auth()->user()->id;    // ID del usuario autenticado
        $log->save();                          // Guarda el registro de log

        // Retorna respuesta JSON con el resultado
        return response()->json([
            'status' => 'success',
            'message' => 'Empresa creada con exito.',
        ]);

        /*Company::create($request->all());

        return redirect()->route('companies.index')->with('success', 'Empresa creada exitosamente.');*/
    }

    /**
     * Muestra la vista para visualizar empresas.
     *
     * @return \Illuminate\View\View Vista de empresas
     */
    public function viewCompanies()
    {
        return view('companies.view_companies');
    }

    /**
     * Devuelve una lista de todas las empresas en formato JSON.
     * Incluye URLs para acceder a los detalles de cada empresa.
     *
     * @return JsonResponse Lista de empresas con sus detalles
     */
    public function listCompanies(): JsonResponse
    {
        $compania = Company::all();
        $companies = [];
        foreach ($compania as $company) {
            $companies[] = [
                'id' => $company->id,
                'name' => $company->name,
                'description' => $company->description,
                'url_detail' => route('companies.show', $company->id),
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' => $companies,
        ]);
    }

    /**
     * Muestra los detalles de una empresa específica.
     *
     * @param int $id ID de la empresa a mostrar
     * @return \Illuminate\View\View Vista con los detalles de la empresa
     */
    public function show($id)
    {
        $company = Company::find($id);
        return view('companies.show', [
            'company' => $company,
        ]);
    }

    /**
     * Muestra el formulario para editar una empresa existente.
     *
     * @param int $id ID de la empresa a editar
     * @return \Illuminate\View\View Vista del formulario de edición
     */
    public function edit($id)
    {
        $company = Company::find($id);
        $products = Product::all();
        return view('companies.edit', [
            'company' => $company,
            'products' => $products,
        ]);
    }

    /**
     * Actualiza la información de una empresa existente.
     *
     * @param int $id ID de la empresa a actualizar
     * @param Request $request Datos actualizados de la empresa
     * @return JsonResponse Respuesta con el resultado de la operación
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $company= Company::find($id);
        $company->name = $request->name;
        $company->description = $request->description;
        $company->save();

        $company->products()->sync($request->products ?? []);

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa actualizada con exito.',
        ]);

       /* $company = Company::findOrFail($id);
        $company->update($request->all());

        return redirect()->route('companies.index')->with('success', 'Empresa actualizada correctamente.');*/
    }

    /**
     * Elimina una empresa del sistema.
     * Registra la eliminación en el log del sistema antes de eliminar la empresa.
     *
     * @param int $id ID de la empresa a eliminar
     * @return JsonResponse Respuesta con el resultado de la operación
     */
    public function destroy($id)
    {
        $company = Company::find($id);

        $log = new Log();
        $log->action = 'ELIMINAR';
        $log->objeto = 'Empresas';
        $log->objeto_id =  $company->id;
        $log->detail = $company->toJson();
        $log->ip = '4444';
        $log->user_id = auth()->user()->id;
        $log->save();

        $company->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa eliminada con exito.',
        ]);

        //return redirect()->route('companies.index')->with('success', 'Empresa eliminada.');
    }

    private function middleware(string $string)
    {
    }

}
