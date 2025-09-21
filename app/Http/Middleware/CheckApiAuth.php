<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
    // 1) Cabecera personalizada (método legacy, puedes eliminarla luego)
    if ($request->header('api_token') === 'authenticated') {
        return $next($request);
    }

    // 2) Bearer token: busca el usuario en api_token
    $token = $request->bearerToken();
    if ($token && ($user = \App\Models\User::where('api_token', $token)->first())) {
		auth()->setUser($user);
		return $next($request);
}

    // 3) (opcional) chequeo de sesión para acceso directo al navegador
    if (Session::has('user_authenticated')) {
        return $next($request);
    }

    // Ningún método válido → 401 JSON
    return response()->json([
        'success' => false,
        'message' => 'No autenticado. Debes iniciar sesión.',
        'error'   => 'Unauthorized'
    ], 401);
}
}
