<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\View\View;

class HomeController extends Controller
{
    final public function index(): View
    {
        return view('welcome');
    }

    final public function dashboard(): View
    {
        $customer = auth()->user()?->customer;

        $companies = Company::with('products')->get();
        return view('dashboard', [
            'companies' => $companies,
            'customer' => $customer
        ]);
    }
}
