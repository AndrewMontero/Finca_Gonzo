<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($cred, $remember)) {
            // Evita 419 y fija sesión nueva tras login
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false) ?? '/');
        }

        return back()
            ->withErrors(['email' => 'Credenciales inválidas.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validaciones alineadas a la BD:
        // telefono VARCHAR(30), ubicacion VARCHAR(150) -> pueden ser null
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:150', 'unique:users,email'],
            'password'   => ['required', 'confirmed', 'min:6'],
            'telefono'   => ['nullable', 'string', 'max:30'],
            'ubicacion'  => ['nullable', 'string', 'max:150'],
        ]);

        // Crea el usuario con rol cliente
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'rol'      => 'cliente',
        ]);

        // Asegura valores NULL si vienen vacíos
        $telefono  = $request->filled('telefono')  ? $data['telefono']  : null;
        $ubicacion = $request->filled('ubicacion') ? $data['ubicacion'] : null;

        // Crea el Cliente enlazado si no existe
        Cliente::firstOrCreate(
            ['correo' => $user->email],
            [
                'nombre'    => $user->name,
                'telefono'  => $telefono,
                'ubicacion' => $ubicacion,
            ]
        );

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
