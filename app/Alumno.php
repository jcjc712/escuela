<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 't_alumnos';
    protected $primaryKey = 'id_t_usuarios';
    public function calificaciones(){
        return $this->hasMany('App\Calificacion', 'id_t_usuarios', 'id_t_usuarios');
    }

    protected $fillable = [
        'id_t_usuarios',
    ];

    public function getFechaRegistroAttribute($value)
    {
        return date('d/m/Y',strtotime($value));
    }
}
