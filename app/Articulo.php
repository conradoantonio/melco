<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'articulos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tipo_articulo_id', 'nombre', 'stock', 'descripcion', 'precio', 'puntuacion', 'imagen', 'weight'];

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
        return $this->belongsTo('App\TipoArticulo', 'tipo_articulo_id');
    }

    /**
     * Get the portions related to the record
     *
     */
    public function imagenes()
    {
        return $this->hasMany('App\ArticuloImagen', 'articulo_id');
    }

    /**
     * Get the users filtered by the given values.
     */
    static function filter_rows(/*$l_usr, */$limit = null, $page = null, $tipo_id = null, $search = null, $order_by = null)
    {
        $rows = Articulo::query();

        /*if ( $l_usr->id_role == 1 ) {#Admin
            //
        } elseif ( $l_usr->id_role == 2 ) {#Admin
            //
        } else {#Any other role wouldn't be able to get any data
            return [];
        }*/

        if ( $page ) {
            $offset = ( $limit ? ( ((int)$page - 1) * (int)$limit) : 0 );

            $rows = $rows->offset($offset); 
        }

        if ( $limit ) { $rows = $rows->limit($limit); }

        if ( $tipo_id !== null ) {
            $rows = $rows->where('tipo_articulo_id', $tipo_id);
        }

        if ( $search !== null ) {
            $rows = $rows->where('nombre', 'like', '%'.$search.'%');
        }

        if ( $order_by == 'precio_mayor_menor' ) { $rows = $rows->orderBy('precio', 'desc'); }
        elseif ( $order_by == 'precio_menor_mayor' ) { $rows = $rows->orderBy('precio', 'asc'); }

        return $rows->with(['tipo'])->orderBy('destacado', 'desc')->get();
    }
}
