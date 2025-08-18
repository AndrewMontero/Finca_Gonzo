<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $users = User::query()
            ->when($q, fn($qry) =>
                $qry->where('name','like',"%$q%")
                    ->orWhere('email','like',"%$q%")
            )
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users','q'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'rol' => ['required','in:cliente,repartidor,admin'],
        ]);

        $user->update(['rol' => $request->rol]);

        return back()->with('success', 'Rol actualizado');
    }
}
