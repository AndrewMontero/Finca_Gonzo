<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'repartidor_id',
        'fecha_hora',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
