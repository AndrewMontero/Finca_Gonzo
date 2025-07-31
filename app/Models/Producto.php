<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'unidad_medida',
        'precio_unitario',
        'stock_minimo',
        'stock_maximo',
        'stock_actual',
    ];

    public function entregas()
    {
        return $this->belongsToMany(Entrega::class, 'detalle_entrega')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
