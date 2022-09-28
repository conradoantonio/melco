<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tipo_pago';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['descripcion', 'clase'];

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
        return $this->hasMany('App\Pedido', 'tipo_pago_id');
    }

    /**
     * Get the orders related to the record
     *
     */
    public function ordenes()
    {
        return $this->hasMany('App\Pedido', 'tipo_pago_id');
    }
}
