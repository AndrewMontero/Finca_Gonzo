<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    // ✅ IMPORTANTE: 'estado' debe estar en fillable
    protected $fillable = [
        'cliente_id',
        'repartidor_id',
        'fecha_hora',
        'estado',
    ];

    // Cast para manejar fecha como instancia Carbon
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    // --- Relaciones ---
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

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_entrega')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    // --- Accessors útiles ---
    public function getRepartidorNombreAttribute()
    {
        return $this->repartidor?->name ?? 'Sin asignar';
    }

    public function getClienteNombreAttribute()
    {
        return $this->cliente?->nombre ?? 'Cliente no disponible';
    }

    public function getEstadoFormateadoAttribute()
    {
        return ucfirst($this->estado);
    }

    public function getEstadoBadgeClassAttribute()
    {
        return match ($this->estado) {
            'realizada' => 'text-bg-success',
            'cancelada' => 'text-bg-danger',
            default     => 'text-bg-warning',
        };
    }

    // --- Scopes útiles ---
    public function scopePorEstado($q, $estado)   
    { 
        return $q->where('estado', $estado); 
    }
    
    public function scopePendientes($q)           
    { 
        return $q->where('estado', 'pendiente'); 
    }
    
    public function scopeRealizadas($q)           
    { 
        return $q->where('estado', 'realizada'); 
    }
    
    public function scopeCanceladas($q)           
    { 
        return $q->where('estado', 'cancelada'); 
    }
    
    public function scopePorRepartidor($q, $id)   
    { 
        return $q->where('repartidor_id', $id); 
    }

    // --- Lógica de negocio ---
    public function calcularTotal()
    {
        $total = 0;
        foreach ($this->productos as $producto) {
            $cantidad = $producto->pivot->cantidad;
            $total += $producto->precio_unitario * $cantidad;
        }
        return $total;
    }

    public function tieneRepartidor()   
    { 
        return !is_null($this->repartidor_id) && $this->repartidor !== null; 
    }
    
    public function puedeSerEditada()   
    { 
        return $this->estado !== 'realizada'; 
    }
    
    public function puedeSerCancelada() 
    { 
        return $this->estado === 'pendiente'; 
    }

    public function marcarComoRealizada()
    {
        if ($this->estado !== 'realizada') {
            $this->update(['estado' => 'realizada']);

            foreach ($this->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $producto->decrement('stock_actual', $cantidad);
            }

            if (!$this->factura) {
                $total = $this->calcularTotal();
                \App\Models\Factura::create([
                    'entrega_id' => $this->id,
                    'subtotal'   => $total,
                    'total'      => $total,
                ]);
            }
            return true;
        }
        return false;
    }

    public function cancelar()
    {
        if ($this->puedeSerCancelada()) {
            $this->update(['estado' => 'cancelada']);
            return true;
        }
        return false;
    }
}