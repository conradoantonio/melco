<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Porcion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_porciones_fast_food';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre_porcion', 'precio'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the products related to the record
     *
     */
    public function productos()
    {
        return $this->belongsToMany('App\ProductoFastFood', 't_extras_fast_food', 'id_porcion', 'id_producto')->withPivot('cantidad');
    }
}
