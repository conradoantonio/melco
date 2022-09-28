<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'direcciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'pais', 'estado', 'ciudad', 'codigo_postal', 'colonia', 'calle', 'referencias', 'latitud', 'longitud', 'status'];

    /**
     * Get the users related to the record
     *
     */
    public function user()
    {
        return $this->hasMany('App\User', 'user_id');
    }
}
