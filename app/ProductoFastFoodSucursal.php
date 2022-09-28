<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoFastFoodSucursal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_producto_fast_food_sucursal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_sucursales', 'id_producto', 'cantidad'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the franchise related to the record
     *
     */
    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal', 'id_sucursales');
    }

    /**
     * Get the product related to the record
     *
     */
    public function producto()
    {
        return $this->belongsTo('App\ProductoSuper', 'id_producto');
    }
}
