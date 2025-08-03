<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;

    protected $fillable = ['usuario_id', 'accion'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
