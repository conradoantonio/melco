<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPedido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_tipo_pedido';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tipo'];

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
        return $this->hasMany('App\Pedido', 'id_tipo_pedido');
    }
}
