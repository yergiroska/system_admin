<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $companies = Company::all();
        return view('companies.index', [
            'companies' => $companies
        ]);
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $company= new Company();
        $company->name = $request->name;
        $company->description = $request->description;
        $company->save();

        $log = new Log();
        $log->action = 'CREAR';
        $log->objeto = 'Empresas';
        $log->objeto_id =  $company->id;
        $log->detail = $company->toJson();
        $log->ip = '4444';
        $log->user_id = auth()->user()->id;
        $log->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa creada con exito.',
        ]);

        /*Company::create($request->all());

        return redirect()->route('companies.index')->with('success', 'Empresa creada exitosamente.');*/
    }

    public function viewCompanies()
    {
        return view('companies.view_companies');
    }

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

    public function show($id)
    {
        $company = Company::find($id);
        return view('companies.show', [
            'company' => $company,
        ]);
    }

    public function edit($id)
    {
        $company = Company::find($id);
        return view('companies.edit', [
            'company' => $company
        ]);
    }

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

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa actualizada con exito.',
        ]);

       /* $company = Company::findOrFail($id);
        $company->update($request->all());

        return redirect()->route('companies.index')->with('success', 'Empresa actualizada correctamente.');*/
    }

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
