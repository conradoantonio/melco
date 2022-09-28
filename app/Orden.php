<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ordenes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['skydropx_label_id', 'user_id', 'status_pedido_id', 'direccion_id', 'tipo_pago_id', 'token_pago', 'num_tarjeta', 'fecha', 'total'];

    /**
     * Get the users related to the record
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get the status related to the record
     *
     */
    public function status()
    {
        return $this->belongsTo('App\StatusPedido', 'status_pedido_id');
    }

    /**
     * Get the address related to the record
     *
     */
    public function direccion()
    {
        return $this->belongsTo('App\Direccion', 'direccion_id');
    }

    /**
     * Get the payment type related to the record
     *
     */
    public function tipo_pago()
    {
        return $this->belongsTo('App\TipoPago', 'tipo_pago_id');
    }

    /**
     * Get the details related to the record
     *
     */
    public function detalles()
    {
        return $this->hasMany('App\OrdenDetalle', 'orden_id');
    }

    /**
     * Get the spei data related to the record
     *
     */
    public function spei_data()
    {
        return $this->morphOne(SpeiData::class, 'speiable');
    }

    /**
    * Get the cancelation data related to the record
    *
    */
    public function cancelacion()
    {
      return $this->morphOne(Cancelacion::class, 'cancelable');
    }

    /**
    * Get the users filtered by the given values.
    */
    static function filter_rows($l_usr, $status_reject = [], $tipo_pago_id = null, $status_pedido_id = null, $fecha_inicio = null, $fecha_fin = null)
    {
        if ($l_usr->role_id == 1) { #Admin
            $rows = Orden::query();
        } elseif ($l_usr->role_id == 2) { #Franquiciatario
            /*$ids = Sucursal::where('id', $l_usr->id_sucursales)->get();
            $rows = Orden::whereIn('id_sucursales', $ids);*/
            return [];
        } else { #Any other role wouldn't be able to get any data
            return [];
        }

        if (count($status_reject)) {
            $rows = $rows->whereNotIn('status_pedido_id', $status_reject);
        }

        if ($tipo_pago_id !== null) {
            $rows = $rows->where('tipo_pago_id', $tipo_pago_id);
        }

        if ($status_pedido_id !== null) {
            $rows = $rows->where('status_pedido_id', $status_pedido_id);
        }

        if ($fecha_inicio !== null) {
            $rows = $rows->where('fecha', '>=', $fecha_inicio.' 00:00:00');
            #$rows = $rows->where('fecha', '>=', $fecha_inicio);
        }

        if ($fecha_fin !== null) {
            $rows = $rows->where('fecha','<=', $fecha_fin.' 23:59:59');
            #$rows = $rows->where('fecha', '<=', $fecha_fin);
        }

        return $rows->get();
    }
}
