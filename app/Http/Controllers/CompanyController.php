<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
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

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa creada con exito.',
        ]);

        /*Company::create($request->all());

        return redirect()->route('companies.index')->with('success', 'Empresa creada exitosamente.');*/
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('companies.edit', [
            'company' => $company
        ]);
    }

    public function update(Request $request, $id)
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
        $company->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Empresa eliminada con exito.',
        ]);

        //return redirect()->route('companies.index')->with('success', 'Empresa eliminada.');
    }

}
