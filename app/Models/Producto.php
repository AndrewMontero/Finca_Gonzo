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
        'stock_actual'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'stock_actual' => 'integer'
    ];

    /**
     * Relación con entregas (si existe tabla pivot detalle_entregas)
     */
    public function entregas()
    {
        return $this->belongsToMany(Entrega::class, 'detalle_entregas')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal')
                    ->withTimestamps();
    }

    /**
     * Accessor para verificar si el stock está bajo
     */
    public function getStockBajoAttribute()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Accessor para calcular el porcentaje de stock
     */
    public function getPorcentajeStockAttribute()
    {
        if ($this->stock_maximo <= 0) {
            return 0;
        }

        return round(($this->stock_actual / $this->stock_maximo) * 100, 2);
    }

    /**
     * Accessor para calcular el valor total del inventario de este producto
     */
    public function getValorInventarioAttribute()
    {
        return $this->stock_actual * $this->precio_unitario;
    }

    /**
     * Scope para productos con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock_actual', '<=', 'stock_minimo');
    }

    /**
     * Scope para productos con stock normal
     */
    public function scopeStockNormal($query)
    {
        return $query->whereColumn('stock_actual', '>', 'stock_minimo');
    }
}
