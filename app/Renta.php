<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    protected $table = 'rentas';
    protected $fillable = [
        'acceso',
        'hours',
        'salida',
        'placa',
        'modelo',
        'marca',
        'color',
        'llaves',
        'total',
        'efectivo',
        'cambio',
        'user_id',
        'vehiculo_id',
        'tarifa_id',
        'barcode',
        'estatus',
        'descripcion',
        'cajon_id',
        'direccion'
    ];

    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class);
    }
}
