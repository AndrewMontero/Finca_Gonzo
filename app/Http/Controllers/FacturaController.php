<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        // Aquí puedes agregar la lógica para mostrar las facturas
        return view('facturas.index'); // Asegúrate de que esta vista exista
    }
}
