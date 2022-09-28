<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sucursales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['bd', 'nombre_sucursal', 'direccion', 'lat_long', 'cod_postal'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the orders related to the record
     *
     */
    public function pedidos()
    {
        return $this->hasMany('App\Pedido', 'id_sucursales');
    }

    /**
     * The products that belong to the model.
     */
    public function productos_fastfood()
    {
        return $this->belongsToMany('App\ProductoFastFood', 't_producto_fast_food_sucursal', 'id_sucursales', 'id_producto_fast_food')->withPivot('cantidad');
    }
}
