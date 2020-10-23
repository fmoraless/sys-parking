<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClienteVehiculo extends Model
{
    protected $table = 'cliente_vehiculos';
    protected $fillable = [
        'user_id',
        'vehiculo_id'
    ];
}
