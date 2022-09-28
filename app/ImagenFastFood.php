<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImagenFastFood extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_imagen_fast_food';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_producto', 'imagen'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the users related to the record
     *
     */
    public function producto()
    {
        return $this->belongsTo('App\ProductoFastFood', 'id_producto');
    }
}
