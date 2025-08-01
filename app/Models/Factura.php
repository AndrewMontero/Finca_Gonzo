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

    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }
}
