<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ✅ Listado con búsqueda simple
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Roles permitidos en el select
        $roles = ['cliente' => 'Cliente', 'repartidor' => 'Repartidor', 'admin' => 'Admin'];

        return view('admin.users.index', compact('users', 'roles', 'q'));
    }

    // ✅ Cambiar el rol (cliente <-> repartidor, y también admin si lo necesitas)
    public function updateRole(Request $request, \App\Models\User $user)
    {
        $data = $request->validate([
            'rol' => ['required', 'in:cliente,repartidor,admin'],
        ]);

        $user->update(['rol' => $data['rol']]);

        return back()->with('success', 'Rol actualizado.');
    }
}
