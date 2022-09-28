<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoArticulo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_articulo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre', 'image'];

    /**
     * Get the users related to the record
     *
     */
    public function articulos()
    {
        return $this->hasMany('App\Articulo', 'tipo_articulo_id');
    }
}
