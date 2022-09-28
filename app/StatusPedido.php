<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusPedido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_pedido';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['descripcion'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the orders of products related to the record
     *
     */
    public function pedidos()
    {
        return $this->hasMany('App\Pedido', 'status_pedido_id');
    }

    /**
     * Get the orders of articles related to the record
     *
     */
    public function ordenes()
    {
        return $this->hasMany('App\Orden', 'status_pedido_id');
    }
}
