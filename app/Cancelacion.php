<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cancelacion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cancelaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cancelable_id', 'cancelable_type', 'user_id', 'comentario', 'respuesta', 'status',
    ];

    /**
     * Get the user related to the record
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Obtiene la orden o el pedido asociado al registro
     *
     */
    public function cancelable()
    {
        return $this->morphTo();
    }
}
