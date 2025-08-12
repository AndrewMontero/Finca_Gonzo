<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrega_id',
        'subtotal',
        'total',
    ];

    /**
     * Relación con Entrega
     */
    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }

    /**
     * Acceso directo al cliente a través de la entrega
     */
    public function cliente()
    {
        return $this->hasOneThrough(
            \App\Models\Cliente::class,
            Entrega::class,
            'id', // Clave foránea en la tabla entregas
            'id', // Clave foránea en la tabla clientes
            'entrega_id', // Clave local en facturas
            'cliente_id' // Clave local en entregas
        );
    }
}
