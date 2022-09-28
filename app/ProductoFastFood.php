<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoFastFood extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_producto_fast_food';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'art_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['art_id', 'clave', 'id_categorias', 'descripcion', 'precio1', 'imagen_principal'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the portions related to the record
     *
     */
    public function categoria()
    {
        return $this->belongsTo('App\Categoria', 'id_categorias');
    }

    /**
     * Get the portions related to the record
     *
     */
    public function porciones()
    {
        return $this->belongsToMany('App\Porcion', 't_extras_fast_food', 'id_producto', 'id_porcion')->withPivot('cantidad');
    }

    /**
     * The franchises that belong to the model.
     */
    public function sucursales()
    {
        return $this->belongsToMany('App\Sucursal', 't_producto_fast_food_sucursal', 'id_producto_fast_food', 'id_sucursales')->withPivot('cantidad');
    }

    /**
     * Get the portions related to the record
     *
     */
    public function imagenes()
    {
        return $this->hasMany('App\ImagenFastFood', 'id_producto');
    }

    /**
     * Get the users filtered by the given values.
     */
    static function filter_rows($l_usr, $sucursal_id = null, $id_categorias = null)
    {
        if ( $l_usr->id_role == 1 ) {#Admin
            $rows = ProductoFastFood::query();
        } elseif ( $l_usr->id_role == 2 ) {#Franchise
            $id_sucursales = $l_usr->id_sucursales;
            $ids_productos = ProductoFastFoodSucursal::where('id_sucursales', $id_sucursales)->pluck('id_producto');
            $rows = ProductoFastFood::whereIn('art_id', $ids_productos);
        } else {#Any other role wouldn't be able to get any data
            return [];
        }

        if ( $sucursal_id !== null ) {
            $ids_productos = ProductoFastFoodSucursal::where('id_sucursales', $sucursal_id)->pluck('id_producto');
            $rows = $rows->whereIn('art_id', $ids_productos);
        }

        if ( $id_categorias !== null ) {
            $rows = $rows->where('id_categorias', $id_categorias);
        }

        return $rows->get();
    }
}
