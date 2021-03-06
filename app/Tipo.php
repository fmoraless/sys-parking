<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'tipos';
    protected $fillable = [
        'descripcion', 'image'
        ];

    public function cajon()
    {
        return $this->belongsTo(Cajon::class, 'tipo_id');
    }

    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class, 'tipo_id');
    }
}
