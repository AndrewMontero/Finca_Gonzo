<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'ubicacion',
    ];

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }
}
