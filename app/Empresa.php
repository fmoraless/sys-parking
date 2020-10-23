<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'logo',
    ];

    //Funciones y relaciones
}
