<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $logs = Log::latest()->get();
        return view('logs.index', [
            'logs' => $logs
        ]);
    }

    public function details(int $id)
    {
        $log = Log::find($id);
        $details = json_decode($log->detail);
        return view('logs.details',[
            'log' => $log,
            'details' => $details
        ]);
    }

    private function middleware(string $string)
    {
    }
}
