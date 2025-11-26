<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'Cliente';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'Nombres',
        'Correo',
        'Passwordd'
    ];

    protected $hidden = [
        'Passwordd'
    ];

    public function codigos()
    {
        return $this->hasMany(CodigoVerificacion::class, 'idCliente', 'id');
    }
}
