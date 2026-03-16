<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private array $demoUsers = [
        'admin@demo.com'   => ['password' => 'demo123', 'name' => 'Administrator',   'role' => 'admin',   'avatar' => 'A'],
        'manager@demo.com' => ['password' => 'demo123', 'name' => 'Budi Santoso',    'role' => 'manager', 'avatar' => 'B'],
        'staff@demo.com'   => ['password' => 'demo123', 'name' => 'Siti Rahayu',     'role' => 'staff',   'avatar' => 'S'],
    ];

    public function showLogin()
    {
        if (Session::has('demo_user')) {
            return Session::get('demo_user.role') === 'admin'
                ? redirect()->route('admin.users.index')
                : redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email    = $request->email;
        $password = $request->password;

        if (isset($this->demoUsers[$email]) && $this->demoUsers[$email]['password'] === $password) {
            $user          = $this->demoUsers[$email];
            $user['email'] = $email;

            Session::put('demo_user', $user);
            Session::put('demo_login_time', now()->format('d M Y H:i'));

            $welcomeMsg = 'Selamat datang, ' . $user['name'] . '! Login sebagai ' . strtoupper($user['role']) . '.';

            if ($user['role'] === 'admin') {
                return redirect()->route('admin.users.index')
                    ->with('success', $welcomeMsg . ' Anda masuk ke portal Admin.');
            }

            return redirect()->route('dashboard')
                ->with('success', $welcomeMsg);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password tidak valid. Gunakan akun demo yang tersedia.']);
    }

    public function logout(Request $request)
    {
        $userName = Session::get('demo_user.name', 'User');
        Session::forget('demo_user');
        Session::forget('demo_login_time');

        return redirect()->route('login')
            ->with('success', 'Sampai jumpa, ' . $userName . '! Anda telah berhasil logout.');
    }
}
