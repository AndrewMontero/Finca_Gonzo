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

    // Cast para fechas
    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación con Repartidor (User)
    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    // Relación con Factura
    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    // Relación con Productos (muchos a muchos)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_entrega')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    // Accessor para obtener el nombre del repartidor de forma segura
    public function getRepartidorNombreAttribute()
    {
        return $this->repartidor ? $this->repartidor->name : 'Sin asignar';
    }

    // Accessor para obtener el nombre del cliente de forma segura
    public function getClienteNombreAttribute()
    {
        return $this->cliente ? $this->cliente->nombre : 'Cliente no disponible';
    }

    // Accessor para obtener el estado formateado
    public function getEstadoFormateadoAttribute()
    {
        return ucfirst($this->estado);
    }

    // Accessor para obtener la clase CSS del badge según el estado
    public function getEstadoBadgeClassAttribute()
    {
        switch ($this->estado) {
            case 'realizada':
                return 'badge-success';
            case 'cancelada':
                return 'badge-danger';
            case 'pendiente':
            default:
                return 'badge-warning';
        }
    }

    // Scope para filtrar por estado
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Scope para filtrar entregas pendientes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    // Scope para filtrar entregas realizadas
    public function scopeRealizadas($query)
    {
        return $query->where('estado', 'realizada');
    }

    // Scope para filtrar entregas canceladas
    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    // Scope para filtrar por repartidor
    public function scopePorRepartidor($query, $repartidorId)
    {
        return $query->where('repartidor_id', $repartidorId);
    }

    // Método para calcular el total de la entrega
    public function calcularTotal()
    {
        $total = 0;
        foreach ($this->productos as $producto) {
            $cantidad = $producto->pivot->cantidad;
            $total += $producto->precio_unitario * $cantidad;
        }
        return $total;
    }

    // Método para verificar si la entrega tiene repartidor asignado
    public function tieneRepartidor()
    {
        return !is_null($this->repartidor_id) && $this->repartidor !== null;
    }

    // Método para verificar si la entrega puede ser editada
    public function puedeSerEditada()
    {
        return $this->estado !== 'realizada';
    }

    // Método para verificar si la entrega puede ser cancelada
    public function puedeSerCancelada()
    {
        return $this->estado === 'pendiente';
    }

    // Método para marcar la entrega como realizada
    public function marcarComoRealizada()
    {
        if ($this->estado !== 'realizada') {
            $this->update(['estado' => 'realizada']);

            // Actualizar stock de productos
            foreach ($this->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $producto->decrement('stock_actual', $cantidad);
            }

            // Crear factura si no existe
            if (!$this->factura) {
                $total = $this->calcularTotal();
                \App\Models\Factura::create([
                    'entrega_id' => $this->id,
                    'subtotal' => $total,
                    'total' => $total
                ]);
            }

            return true;
        }
        return false;
    }

    // Método para cancelar la entrega
    public function cancelar()
    {
        if ($this->puedeSerCancelada()) {
            $this->update(['estado' => 'cancelada']);
            return true;
        }
        return false;
    }
}
