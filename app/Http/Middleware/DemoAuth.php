<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class DemoAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('demo_user')) {
            return redirect()->route('login')
                ->with('info', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        return $next($request);
    }
}
