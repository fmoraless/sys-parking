<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plaza extends Model
{
    protected $table = 'plazas';

    protected $fillable = ['descripcion', 'tipo_id', 'estatus'];

    //Relaciones entre plazas y tipos

    public function tipos()
    {
        return $this->hasMany(Tipo::class);
    }
}
