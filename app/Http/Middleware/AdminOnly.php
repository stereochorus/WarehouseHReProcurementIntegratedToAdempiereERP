<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::get('demo_user.role') !== 'admin') {
            return redirect()->route('dashboard')
                ->with('info', 'Halaman ini hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}
