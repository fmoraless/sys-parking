<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $table = 'tarifas';
    protected $fillable = [
        'tiempo',
        'descripcion',
        'costo',
        'tipo_id',
        'jerarquia',

    ];

    //Funciones y relaciones
    public function rentas()
    {
        return $this->hasMany(Renta::class);
    }

    public function tipo()
    {
        return $this->HasOne(Tipo::class, 'id', 'tipo_id');
    }
}
