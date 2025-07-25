<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'identity_document' => 'required|unique:customers',
        ]);

        $customer = new Customer();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->birth_date = $request->birth_date;
        $customer->identity_document = $request->identity_document;
        $customer->save();

        $log = new Log();
        $log->action = 'crear usuario';
        $log->detail = $customer->toJson();
        $log->ip = '1111';
        $log->user_id = auth()->user()->id;
        $log->save();

       return response()->json([
           'status' => 'success',
           //'data' => $customer,
           'message' => 'Cliente creado con exito',
       ]);

        //return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function viewCustomers()
    {
        return view('customers.view_customers');
    }

    public function listCustomers(): JsonResponse
    {
        $customers = Customer::all();
        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    public function update($id, Request $request,)
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
        $customer->birth_date = $request->birth_date;
        $customer->identity_document = $request->identity_document;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente actualizado con exito',
        ]);

        //return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return response()->json([
                'status' => 'success',
                'message' => 'Cliente eliminado con exito',
            ]);

        //return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
