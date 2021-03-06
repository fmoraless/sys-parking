<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    protected $fillable = [
        'placa',
        'modelo',
        'marca',
        'color',
        'tipo_id'
    ];
}
