<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenDetalle extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orden_detalles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['orden_id', 'articulo_id', 'articulo', 'cantidad', 'precio_u', 'total'];

    /**
     * Get the users related to the record
     *
     */
    public function orden()
    {
        return $this->belongsTo('App\Orden', 'orden_id');
    }

    /**
     * Get the status related to the record
     *
     */
    public function articulo()
    {
        return $this->belongsTo('App\Articulo', 'articulo_id');
    }
}
