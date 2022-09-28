<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_extras_fast_food';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_producto', 'id_porcion', 'cantidad'];

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

    /**
     * Get the users related to the record
     *
     */
    public function porcion()
    {
        return $this->belongsTo('App\Porcion', 'id_porcion');
    }
}
