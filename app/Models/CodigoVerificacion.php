<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CodigoVerificacion extends Model
{
    // Nombre de la tabla
    protected $table = 'CodigoVerificacion';

    // Clave primaria
    protected $primaryKey = 'id';

    // Sin timestamps
    public $timestamps = false;

    // Campos que se pueden llenar
    protected $fillable = [
        'idCliente',
        'Codigo',
        'FechaCaducidad'
    ];

    // Conversión de tipos
    protected $casts = [
        'FechaCaducidad' => 'datetime'
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente', 'id');
    }

    public function estaVigente()
    {
        return Carbon::now()->lessThanOrEqualTo($this->FechaCaducidad);
    }
}
