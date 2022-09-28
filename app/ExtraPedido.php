<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraPedido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_extra_pedido';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_pedido', 'nombre_producto', 'nombre_porcion', 'cantidad', 'precio_u', 'total'];

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
    public function pedido()
    {
        return $this->belongsTo('App\Pedido', 'id_pedido');
    }
}
