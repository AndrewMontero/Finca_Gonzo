<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index()
    {
        $auditorias = Auditoria::with('usuario')->latest()->paginate(10);
        return view('auditorias.index', compact('auditorias'));
    }

    public function destroy(Auditoria $auditoria)
    {
        $auditoria->delete();
        return redirect()->route('auditorias.index')->with('success', 'Registro de auditoría eliminado.');
    }
}
