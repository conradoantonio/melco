<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Pedido;
use App\Tarjeta;
use App\SpeiData;
use App\TipoPago;
use App\DetallePedido;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PedidoController extends Controller
{
    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function payOrderProducts(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $tipoPago = TipoPago::find($req->tipo_pago_id);

        if (! $tipoPago ) { return response(['msg' => 'Especifique un tipo de pago válido', 'status' => 'error'], 200); }

        $orden = Pedido::where('id', $req->pedido_id)->where('user_id', $user->id)->first();

        if (! $orden ) { return response(['msg' => 'ID de orden inválido', 'status' => 'error'], 200); }

        #Verifica el pago de paypal por si ya se debe actualizar
        if ( $req->status_payment && $orden->status->descripcion == 'Esperando pago paypal' ) {

            if ( $req->status_payment == 'success' ) {

                $orden->token_orden = $req->token_pago;
                $orden->paypal_user_id = $req->paypal_user_id;
                $orden->status_pedido_id = 1;

                $orden->save();

                return response(['msg' => 'Status de orden de paypal modificada', 'status' => 'success', 'data' => $orden], 200);

            } else if ( $req->status_payment == 'error' ) {

                $this->changeProductStock($orden->details, 'increment');

                $orden->status_pedido_id = 6;

                $orden->save();

                return response(['msg' => 'Pedido cancelado por falta de pago, vuelva a intentarlo', 'status' => 'success'], 200);

            }
        }

        #Obtiene los productos del carrito
    	$items = app('App\Http\Controllers\API\CartApiController')->itemResponse($orden);

    	#Valida el stock de los productos del carrito
      	$items = app('App\Http\Controllers\API\CartApiController')->checkStock(collect($items));

      	if ( count($items->removeItems) > 0 ) {

      		#Actualiza el carrito y remueve los items sin stock
      		app('App\Http\Controllers\API\CartApiController')->updateItems($orden, $items);

	        return response(['code' => 1866, 'error' => $items->removeItems, 'message' => "No hay stock suficiente para los siguientes artículos", 'removeItems' => $items->removeItems,], 412);
	    }

        if ( $tipoPago->descripcion == 'Tarjeta' ) {

            $tarjeta = Tarjeta::where('id', $req->tarjeta_id)->where('user_id', $user->id)->first();

            if (! $tarjeta ) { return response(['msg' => 'ID de tarjeta inválido', 'status' => 'error'], 200); }

            $res = $this->payOrder( $user, $orden->total, $tarjeta, $req );

            #Pago por openpay falló
            if ( $res['status'] != 'success' ) { return response($res, 200); }

            #Se guarda la información del pago de openpay en una variable
            $response = $res['data'];

            #Se guarda el id de la orden de openpay
            $orden->token_orden = $response->id;
            $orden->num_tarjeta = $tarjeta->numero;
            $orden->tipo_tarjeta = $tarjeta->tipo;
            $orden->status_pedido_id = 1;#Creado
            $orden->total = $response->amount;//Total pagado por cliente en pesos

        } elseif ( $tipoPago->descripcion == 'Paypal' ) {

            $orden->token_orden = null;
            // $orden->total = $req->total;//Total pagado por cliente en CENTAVOS
            $orden->status_pedido_id = 8;#Pendiente de pago paypal

        } elseif ( $tipoPago->descripcion == 'SPEI' ) {

            $res = $this->generateSpeiPayment( $user, $orden->total * 100, time() , $req );

            #Pago por openpay falló
            if ( $res['status'] != 'success' ) { return response($res, 200); }

            #Se guarda la información del pago de openpay en una variable
            $response = $res['data'];

            $orden->total = $response->amount;//Total pagado por cliente en pesos
            $orden->token_orden = $response->id;
            $orden->status_pedido_id = 7;#Procesando pago de spei

            #Se guarda la información del pago de openpay en una variable
            $spei_order = $res['data'];

            $spei_data = New SpeiData;

            $spei_data->speiable_type = Pedido::class;
            $spei_data->type = $spei_order->payment_method->type;
            $spei_data->agreement = $spei_order->payment_method->agreement;
            $spei_data->bank = $spei_order->payment_method->bank;
            $spei_data->clabe = $spei_order->payment_method->clabe;
            $spei_data->name = $spei_order->payment_method->name;
            $spei_data->due_date = $spei_order->due_date;
            $spei_data->due_date_string = strftime('%d', strtotime($spei_order->due_date)).' de '.strftime('%B', strtotime($spei_order->due_date)). ' del '.strftime('%Y', strtotime($spei_order->due_date));

        }

        $orden->user_id = $user->id;
        $orden->tipo_pago_id = $tipoPago->id;
        $orden->fecha = date("Y-m-d H:i:s");
        // $orden->sub_total = $response->amount;

        $orden->save();

        #Se terminan de guardar los datos de spei
        if ( $tipoPago->descripcion == 'SPEI' ) {

            $spei_data->speiable_id = $orden->id;

            $spei_data->save();

        }

        $this->changeProductStock($orden->details, 'decrement');

        return response(['msg' => 'Pedido procesado correctamente', 'status' => 'success', 'data' => $orden->load(['proveedor', 'status', 'metodoPago', 'details', 'spei_data'])], 200);
    }
}
