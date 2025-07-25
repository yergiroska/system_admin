<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::latest()->get();
        return view('logs.index', compact('logs'));
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
}
