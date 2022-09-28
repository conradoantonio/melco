<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticuloImagen extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'articulo_imagen';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['articulo_id', 'url'];

    /**
     * Get the users related to the record
     *
     */
    public function articulo()
    {
        return $this->belongsTo('App\Articulo', 'articulo_id');
    }
}
