<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;


class Pedido extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'pedido';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'proveedor_id', 'user_id', 'status_pedido_id', 'tipo_pago_id',
    'token_orden', 'direccion_cliente', 'fecha', 'sub_total', 'total', 'token_openpay',
  ];

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = false;

  protected $appends = ['objectId'];

  public function getObjectIdAttribute()
  {
    return $this->id;
  }

  /**
   * Get the branch related to the record
   *
   */
  public function proveedor()
  {
    return $this->belongsTo('App\User', 'proveedor_id');
  }

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
   * Get the payment method related to the record
   *
   */
  public function metodoPago()
  {
    return $this->belongsTo('App\TipoPago', 'tipo_pago_id');
  }

  /**
   * Get the products related to the record
   *
   */
  public function products()
  {
    return $this->hasManyThrough(Product::class, DetallePedido::class, 'pedido_id', 'id', 'id', 'producto_id');
  }

  public function variants()
  {
    return $this->hasManyThrough(ProductVariant::class, DetallePedido::class, 'pedido_id', 'id', 'id', 'product_variant_id');
  }

  public function details()
  {
    return $this->hasMany(DetallePedido::class);
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

  public function detailsProducts()
  {
    $this->details->load('product');
  }

  public function getSubtotalAttribute(){
    return $this->details->sum('total');
  }

  public function getTotalAttribute(){
    return $this->details->sum('total');
  }

  public function card(){

    if (strlen($this->token_openpay) >= 10){

     return app('App\Http\Controllers\API\UserController')->arrayCardItem($this->cardOpenPay());
    }

    return $this->token_openpay;
  }

  public function cardOpenPay() {
    Log::info(' token_openpay => ' . $this->token_openpay);
    Log::info(' charges ' . print_r($this->user->getCustomerOpenpay()->charges->get($this->token_openpay)->card, true));

    return $this->user->getCustomerOpenpay()->charges->get($this->token_openpay)->card;
  }

  static function getOrders(User $customer)
  {
    $status_cart = StatusPedido::where('descripcion', 'Cart')->first();

    return Pedido::where('user_id', $customer->id)->whereNotIn('status_pedido_id', [$status_cart->id])->get();
  }

  static function getCart(User $customer)
  {
    $status_cart = StatusPedido::where('descripcion', 'Cart')->first();

    return Pedido::firstOrCreate(
      ['status_pedido_id' => $status_cart->id, 'user_id' => $customer->id],
      [
        'proveedor_id' => 2,
        'tipo_pago_id' => 1,
        'sub_total' => 0,
        'total' => 0
      ]
    );
  }

  /**
   * Get the users filtered by the given values.
   */
  static function filter_rows($l_usr, $status_reject = [], $tipo_pago_id = null, $status_pedido_id = null, $fecha_inicio = null, $fecha_fin = null)
  {
    if ($l_usr->role_id == 1) { #Admin
      $rows = Pedido::query();
    } elseif ($l_usr->role_id == 2) { #Franquiciatario
      $ids = Sucursal::where('id', $l_usr->id_sucursales)->get();
      $rows = Pedido::whereIn('id_sucursales', $ids);
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
      $rows = $rows->whereRaw('fecha >= "' . $fecha_inicio . '"');
      #$rows = $rows->where('fecha', '>=', $fecha_inicio);
    }

    if ($fecha_fin !== null) {
      $rows = $rows->whereRaw('fecha <= "' . $fecha_fin . '"');
      #$rows = $rows->where('fecha', '<=', $fecha_fin);
    }

    return $rows->get();
  }

  /**
   *
   * @return Get the last week sales data
   */
  public static function getLastWeekSales($tipo = null)
  {
    $rows = Pedido::select(DB::raw('SUBSTRING_INDEX(fecha, " ", 1) as fecha_ped, SUM(total) AS "total_paid",
            MONTH(fecha) AS month, DAY(fecha) AS day, COUNT(*) AS num_sales'))
      ->whereRaw('fecha >= SUBDATE(CURDATE(), INTERVAL 6 DAY) ')
      ->whereRaw('DATE(fecha) <= CURDATE() ')
      #->where('status_id', 2)
      ->groupBy(DB::raw('DAY(fecha)'));

    if ($tipo !== null) {
      $rows = $rows->where('id_tipo_pedido', $tipo);
    }

    return $rows->get();
  }

  /**
   *
   * @return Get the last week sales data
   */
  public static function getGroupedSalesByHour($tipo = null, $date)
  {
    $rows = Pedido::select(DB::raw('*, COUNT(fecha) AS total_pedidos, HOUR(fecha) AS hora, DAY(fecha) AS no_dia'))
      ->whereRaw('DATE(fecha) = "' . $date . '"')
      #->whereRaw('fecha >= SUBDATE(CURDATE(), INTERVAL 6 DAY) ')
      #->whereRaw('DATE(fecha) <= CURDATE() ')
      #->where('status_id', 2)
      ->groupBy(DB::raw('HOUR(fecha)'));

    if ($tipo !== null) {
      $rows = $rows->where('id_tipo_pedido', $tipo);
    }
    return $rows->get();
  }
}
