<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoSuper extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_producto_super';

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
    protected $fillable = ['art_id', 'id_tipo_producto', 'id_categorias', 'descripcion', 'clave', 'precio1', 'imagen_principal'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /** Deprecated, not use it
     * Get the portions related to the record
     *
     */
    public function tipo()
    {
        return $this->belongsTo('App\TipoProducto', 'id_tipo_producto');
    }

    /**
     * Get the portions related to the record
     *
     */
    public function categoria()
    {
        return $this->belongsTo('App\Categoria', 'id_categorias');
    }

    /**
     * The franchises that belong to the model.
     */
    public function sucursales()
    {
        return $this->belongsToMany('App\Sucursal', 't_producto_super_sucursal', 'id_producto_super', 'id_sucursales')->withPivot('cantidad');
    }

    /**
     * Get the portions related to the record
     *
     */
    public function imagenes()
    {
        return $this->hasMany('App\ImagenSuper', 'id_producto');
    }

    /**
     * Get the users filtered by the given values.
     */
    static function filter_rows($l_usr, $sucursal_id = null, $id_categorias = null)
    {
        if ( $l_usr->id_role == 1 ) {#Admin
            $rows = ProductoSuper::query();
        } elseif ( $l_usr->id_role == 2 ) {#Admin
            $id_sucursales = $l_usr->id_sucursales;
            $ids_productos = ProductoSuperSucursal::where('id_sucursales', $id_sucursales)->pluck('id_producto_super');
            $rows = ProductoSuper::whereIn('art_id', $ids_productos);
        } else {#Any other role wouldn't be able to get any data
            return [];
        }

        if ( $sucursal_id !== null ) {
            $ids_productos = ProductoSuperSucursal::where('id_sucursales', $sucursal_id)->pluck('id_producto_super');
            $rows = $rows->whereIn('art_id', $ids_productos);
        }

        if ( $id_categorias !== null ) {
            $rows = $rows->where('id_categorias', $id_categorias);
        }

        return $rows->get();
    }
}
