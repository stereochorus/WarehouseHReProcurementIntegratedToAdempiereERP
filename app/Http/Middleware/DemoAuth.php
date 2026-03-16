<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class DemoAuth
{
    // Routes that admin is NOT allowed to access (transaction routes)
    private array $transactionPrefixes = [
        'warehouse', 'hr', 'procurement', 'aset-it', 'e-approval',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('demo_user')) {
            return redirect()->route('login')
                ->with('info', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        // Admin hanya boleh akses /admin/* dan /dashboard
        if (Session::get('demo_user.role') === 'admin') {
            $path = $request->path();
            $isTransactionRoute = collect($this->transactionPrefixes)
                ->contains(fn($prefix) => str_starts_with($path, $prefix));

            if ($isTransactionRoute) {
                return redirect()->route('admin.users.index')
                    ->with('info', 'Admin tidak dapat mengakses menu transaksi. Gunakan akun Manager atau Staff.');
            }
        }

        return $next($request);
    }
}
