<?php

namespace App\Http\Controllers\API;

use DB;

use \App\User;
use \App\Producto;
use \App\Categoria;
use App\DetallePedido;
use App\Http\Controllers\Controller;


use \App\Events\RefreshEvent;
use App\OptionValue;
use App\Pedido;
use App\Product;
use App\ProductVariant;
use App\StatusPedido;
use DateTime;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use function GuzzleHttp\json_encode;

class CartApiController extends Controller
{


  public function getCart(Request $req)
  {

    $data = $req->json()->all();

    $customer_id = $data['where']['customer']['objectId'];
    $cart = Pedido::getCart(User::find($customer_id));

    //if (isset($data['items'])) {
    return $this->updateCart($req, $cart->id);
    //}

    if ($cart) {
      $cart->load('variants');
    } else {
      $status_cart = StatusPedido::where('descripcion', '=', 'Cart')->first();

      $cart = Pedido::create([
        'user_id' => $customer_id,
        'status_pedido_id' => $status_cart->id,
        'proveedor_id'     => 2,
        'tipo_pago_id'     => 1,
        'sub_total'        => 0,
        'total'            => 0,
      ]);
    }

    return response(['results' => [$this->objectResponse($cart)], 'getCart' => []], 200);
  }

  public function updateCart(Request $req, $id)
  {
    $data = $req->json()->all();

    $cart = Pedido::find($id);

    $items = isset($data['items']) ? $data['items'] : $this->itemResponse($cart);

    //if (isset($data['items'])) {

      $items = $this->checkStock(collect($items));

      $this->updateItems($cart, $items);

      if(count($items->removeItems) > 0 ){
        return response(['code' => 1866, 'error' => $items->removeItems, 'message' => "stock unsuficient", 'removeItems' => $items->removeItems,], 412);
      }
    //}

    return response(['results' => [$this->objectResponse($cart)]], 200);
  }


  public function order(Request $req)
  {
    $data = $req->json()->all();

    $customer = null;
    $orders   = null;


    if (isset($data['where'])) {

      if (isset($data['where']['objectId'])) {

        return response(['results' => [$this->objectResponse(Pedido::find($data['where']['objectId']))]], 200);
      }

      $customer = User::find($data['where']['customer']['objectId']);
      $orders = Pedido::getOrders($customer);

      $response = [];

      foreach ($orders as $order) {
        $response[] = $this->objectResponse($order);
      }

      return response(['results' => $response], 200);
    } else {

      $cartId = $data['order']['cart'];
      $items = collect($data['items']);
      $items = $this->checkStock($items);

      $cart = Pedido::find($cartId);

      if (count($items->removeItems) > 0) {
        //$cart['removeItems'] = ;
        $ret = $this->objectResponse($cart);

        $ret['removeItems'] = $items->removeItems;

        return response(['code' => 1866, 'error' => $items->removeItems, 'message' => "stock unsuficient", 'removeItems' => $items->removeItems,], 412);
        return response(['results' => $ret], 412);
      }

      $this->updateItems($cart, $items);

      $paymentMethod = $data['paymentMethod'];

      $customer = User::find($data['order']['customer']);

      if ($paymentMethod == 'Card') {

        $card = $data['card'];

        $chargeData = array(
          'source_id'         => $card['objectId'],
          'method'            => 'card',
          'amount'            => $cart->total,
          'description'       => 'Melwin Fresh Compra en linea ',
          'order_id'          => 'ORDEN-' . str_pad($cart->id, 6, '0', STR_PAD_LEFT),
          'device_session_id' => $data['order']['deviceDataId'],
        );

        $charge = $customer->getCustomerOpenpay()->charges->create($chargeData);

        $this->checkStock($items, true);

        $cart->tipo_pago_id = 1;
        $cart->token_openpay = $charge->id;
        $cart->status_pedido_id = 1;

        $cart->save();
      }
    }

    $contact = $data['contact'];

    return response(['results' => $this->objectResponse($cart)], 200);
  }

  public function objectResponse(Pedido $cart)
  {

    return [
      "objectId"       => $cart->id,
      "items"          => $this->itemResponse($cart),
      "customer"       => [
        "objectId"     => $cart->user_id,
        "status"       => "Active",
      ],
      "card"           => $cart->card(),
      "token_orden"    => $cart->token_orden,
      "num_tarjeta"    => $cart->num_tarjeta,
      "tipo_tarjeta"   => $cart->tipo_tarjeta,
      "paypal_user_id" => $cart->paypal_user_id,
      "status"         => $cart->status->descripcion,
      "tipo"           => $cart->metodoPago,
      "spei_data"      => $cart->spei_data,
      "subtotal"       => $cart->sub_total,
      "total"          => $cart->total,
      "createdAt"      => (new DateTime(date('Y-m-d\TH:i:s', strtotime($cart->fecha)+ (3600 * 6))))->format('Y-m-d\TH:i:s') . '-0600Z',
      "fecha"          => $cart->fecha,
    ];
  }

  public function updateItems($cart, $items)
  {

    DetallePedido::where('pedido_id', $cart->id)
      ->whereIn('product_variant_id', $cart->details->pluck('product_variant_id')
        ->diff($items->pluck('variation_id')))
      ->delete();

    $items->each(function ($variation, $key) use ($cart) {

      $original_variation = ProductVariant::find($variation['variation_id']);

      DetallePedido::updateOrCreate(
        ['pedido_id' => $cart->id, 'product_variant_id' => $variation['variation_id']],
        [
          'producto_id'     => $variation['objectId'],
          'cantidad'        => $variation['qty'],
          'nombre_producto' => $variation['variation_name'],
          'nombre_variante' => $original_variation ? $original_variation->sku : 'N/A',
          'precio_u'        => isset($variation['variation'][0]) ? $variation['variation'][0]['price_sale'] : $variation['variation']['price_sale'],
          'total'           => ($variation['qty'] * (isset($variation['variation'][0]) ? $variation['variation'][0]['price_sale'] : $variation['variation']['price_sale']))
        ]
      );
    });

    $cart->refresh();

    $cart->total = $cart->sub_total = $cart->details->sum('total');
    $cart->save();
  }

  public function checkStock($items, $update = false)
  {

    $removeIds = [];

    $removeItems = [];

    #Log::info(' checkStock items => ' . print_r($items, true));

    foreach ($items as $index => $item) {

      if (isset($item['variation_id'])) {
        $variation = ProductVariant::find($item['variation_id']);

        //$items[$index]['passCheckStock'] = ($variation->stock > $item['qty']) ? true : false;

        if($variation->stock < $item['qty']){
          //$removeIds[] = $index;

          $items->forget($index);

          $removeItems[] = ['item' => $item, 'stock' => $variation->stock];

          continue;
        }

        if ($update) {
          $variation->stock -= $item['qty'];

          $variation->save();
          $variation->refresh();
        }
      }
    }

    #Log::info(' checkStock removeItems => ' . print_r($removeItems, true));

    //$items = $items->diffKeys($removeIds);

    $items->removeItems = $removeItems;

    return $items;
  }

  public function itemResponse($cart)
  {
    $items = [];
    $req = resolve(Request::class);

    foreach ($cart->details as $item) {

      $items[] = [
        'objectId'           =>  $item->producto_id,
        "name"               =>  $item->nombre_producto,
        "price"              =>  $item->precio_u,
        "slug"               =>  $item->nombre_producto,
        "salePrice"          =>  $item->precio_u,
        "qty"                =>  $item->cantidad,
        "amount"             =>  $item->total,
        'variation_id'       =>  $item->productVariant->id,
        'variation_name'     =>  $item->nombre_producto,
        'variation'          =>  [$item->productVariant, "options" => $item->productVariant->optionsValue],
        "featuredImage"      => ["__type" => "File", "name" => basename($item->product->featured_image), "url" => "{$req->getSchemeAndHttpHost()}/{$item->product->featured_image}"],
        "featuredImageThumb" => ["__type" => "File", "name" => basename($item->product->featured_image), "url" => "{$req->getSchemeAndHttpHost()}/{$item->product->featured_image}"],
      ];
    }

    return $items;
  }
}
