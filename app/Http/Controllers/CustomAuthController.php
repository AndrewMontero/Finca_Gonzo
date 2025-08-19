<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomAuthController extends Controller
{
    // Form de login
    public function showLogin()
    {
        return view('auth.login');
    }

    // POST /login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // 👇 redirección según rol (cliente -> tienda, otros -> dashboard)
            $user = auth()->user();
            $destino = ($user && $user->rol === 'cliente' && \Route::has('tienda.index'))
                ? route('tienda.index')
                : route('dashboard');

            return redirect()->intended($destino); // mantiene intended si viene de middleware
        }

        return back()->withErrors([
            'email' => 'Estas credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Form de registro
    public function showRegister()
    {
        return view('auth.register');
    }

    // POST /register  (⬅️ SIEMPRE rol=cliente)
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'rol'      => 'cliente',            // ✅ por defecto CLIENTE
        ]);

        Auth::login($user);

        // 👇 tras registrarse, cliente va a tienda (si existe); fallback a dashboard
        $destino = \Route::has('tienda.index') ? route('tienda.index') : route('dashboard');

        return redirect()->route($destino === route('dashboard') ? 'dashboard' : 'tienda.index')
                         ->with('success','Cuenta creada correctamente.');
    }

    // POST /logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
