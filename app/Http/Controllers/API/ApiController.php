<?php

namespace App\Http\Controllers\API;

use DB;

use \App\Log;
use \App\Faq;
use \App\Info;
use \App\User;
use \App\Orden;
use \App\Banner;
use \App\Pedido;
use \App\Tarjeta;
use \App\Articulo;
use \App\TipoPago;
use \App\Producto;
use \App\SpeiData;
use \App\Direccion;
use \App\Categoria;
use \App\OrdenDetalle;
use \App\Configuration;
use \App\DetallePedido;
use App\Http\Controllers\Controller;

use App\Http\Controllers\SkydropxController;

use \App\Events\RefreshEvent;

use Illuminate\Http\Request;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class ApiController extends Controller
{
    // Production Credentials
    private $apiKey = "QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t";

    // Pre-Production URL
    private $uri = "https://api-demo.skydropx.com/";

    // Production URL
    // private $uri = "https://api.hotelbeds.com/";

    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function getOrders(Request $req)
    {
        // dd('Seguimos');
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $data = Orden::where('user_id', $user->id)->with(['status', 'direccion', 'tipo_pago', 'detalles', 'spei_data'])->orderBy('id', 'DESC')->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Órdenes enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay órdenes por mostrar', 'data' => $data, 'status' => 'error'], 200);
    }

    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function getOrderDetail(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $data = Orden::where('user_id', $user->id)->where('id', $req->order_id)->with(['status', 'direccion', 'tipo_pago', 'detalles', 'spei_data'])->first();

        $skypdropx_label = DB::table('skypdrop_labels')->where('order_id', $req->order_id)->get()->first();
        $skypdropx_label->data = json_decode($skypdropx_label->data);
        $data->skypdropx_label = $skypdropx_label;

        if ( $data ) {
            return response(['msg' => 'Orden enlistada a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No se encontró la orden solicitada', 'status' => 'error'], 200);
    }

    /**
     * Get the shipping cost
     *
     * @param  Request  $request
     */
    public function shippingCost(Request $req)
    {   
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $direccion = Direccion::where('id', $req->direccion_id)->where('user_id', $user->id)->first();

        if (! $direccion ) { return response(['msg' => 'ID de dirección inválido', 'status' => 'error'], 200); }

        $delivery_cost = 0;
        $request_uri = "{$this->uri}v1/quotations";
        $client = new Client();
        $action = "POST";
        $title = "/quotations"; // Shipments

        $dataToSend = [
            "zip_from" => "44160", 
            "zip_to" => $direccion->codigo_postal, 
            "parcel" => [
                "weight" => $req->weight,
                "height" => "30", 
                "width"  => "30", 
                "length" => "30" 
            ], 
            "carriers" => [
                [ "name" => "Estafeta" ], [ "name" => "Fedex" ]
            ]
        ];

        // $dataToSend = [
        //     "address_from" => [
        //         'province' => 'Jalisco', 
        //         'city' => 'Zapopan', 
        //         'name' => 'Edgard Vargas', 
        //         'zip' => '45134', 
        //         'country' => 'MX', 
        //         'address1' => 'Avenida Angel Leaño #3056', 
        //         'company' => 'Bridgestudio', 
        //         'address2' => 'Avenida Juan Gil Preciado', 
        //         'phone' => '3312948789', 
        //         'email' => 'edgard@bridgestudio.mx',
        //     ], 
        //     "parcels" => [
        //         [
        //             'weight' => $req->weight, 
        //             'distance_unit' => 'CM', 
        //             'mass_unit' => 'KG', 
        //             'height' => 30, 
        //             'width' => 30, 
        //             'length' => 30, 
        //         ]
        //     ],
        //     "address_to" => [
        //         'province' => $direccion->estado, 
        //         'city' => $direccion->ciudad, 
        //         'name' => $user->fullname, 
        //         'zip' => $direccion->codigo_postal, 
        //         'country' => $direccion->pais, 
        //         'address1' => $direccion->calle, 
        //         'company' => 'Melcowin', 
        //         'address2' => $direccion->colonia, 
        //         'phone' => $user->phone ?? '33362772', 
        //         'email' => $user->email,
        //         'reference' => 'Hola',
        //         // 'reference' => $direccion->referencias,
        //         'contents' => 'Producto con embalaje',
        //     ], 
        //     "consignment_note_class_code" => "53131600", 
        //     "consignment_note_packaging_code" => "1H1", 
        //     "carriers" => [
        //         [ "name" => "DHL" ], [ "name" => "Fedex" ]
        //     ]
        // ];

        $response = Http::withHeaders([
            'Authorization' => 'Token token=QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t',
            'Content-Type' => 'application/json',
        ])->post( $request_uri, $dataToSend);


        $data = json_decode($response->getBody());
        $status_code = $response->getStatusCode();

        if ( $data[0] ) {
            $delivery_cost = $data[0]->total_pricing;
            return response(['msg' => 'Costo de envío enlistado a continuación', 'status' => 'success', 'data' => ['costo' => $delivery_cost, 'moneda' => 'MXN']], 200);
        } else {
            return response(['msg' => 'No se pudo cotizar el envío con los datos proporcionados, verifique su información', 'status' => 'error'], 200);
        }
    }

    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function processOrder(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $direccion = Direccion::where('id', $req->direccion_id)->where('user_id', $user->id)->first();

        if (! $direccion ) { return response(['msg' => 'ID de dirección inválido', 'status' => 'error'], 200); }

        $tipoPago = TipoPago::find($req->tipo_pago_id);

        if (! $tipoPago ) { return response(['msg' => 'Especifique un tipo de pago válido', 'status' => 'error'], 200); }

        #Empieza el proceso para el pago

        $orden = New Orden;

        $products_data = $this->getArticlesData($req->articulos);

        // $products_data = array(
        //     'pro_array' => array('Seguimos' => 'Siguiendo'),
        //     'invalid_items' => array('Seguiremos' => 'Siguiendo'),
        //     'total' => '1',
        // );

        #No stock or no existing products
        if ( count($products_data['invalid_items']) ) {
            return response(['msg' => 'No hay stock suficiente para los siguientes artículos', 'status' => 'error', 'data' => $products_data['invalid_items']], 200); 
        }

        if ( $tipoPago->descripcion == 'Tarjeta' ) {

            $tarjeta = Tarjeta::where('id', $req->tarjeta_id)->where('user_id', $user->id)->first();

            if (! $tarjeta ) { return response(['msg' => 'ID de tarjeta inválido', 'status' => 'error'], 200); }

            $res = $this->payOrder( $user, $req->total / 100, $tarjeta, $req );

            #Pago por openpay falló
            if ( $res['status'] != 'success' ) { return response($res, 200); }

            #Se guarda la información del pago de openpay en una variable
            $response = $res['data'];

            #Se guarda el id de la orden de openpay
            $orden->token_pago = $response->id;
            $orden->num_tarjeta = $tarjeta->numero;
            $orden->tipo_tarjeta = $tarjeta->tipo;
            $orden->status_pedido_id = 1;#Creado
            $orden->total = $response->amount * 100;//Total pagado por cliente en CENTAVOS

        } elseif ( $tipoPago->descripcion == 'Paypal' ) {

            $orden->token_pago = null;
            $orden->total = $req->total; //Total pagado por cliente en CENTAVOS
            $orden->status_pedido_id = 8; #Pendiente de pago paypal
            // $orden->status_pedido_id = 1; 

        } elseif ( $tipoPago->descripcion == 'SPEI' ) {

            $res = $this->generateSpeiPayment( $user, $req->total / 100, time() , $req );

            #Pago por openpay falló
            if ( $res['status'] != 'success' ) { return response($res, 200); }

            #Se guarda la información del pago de openpay en una variable
            $response = $res['data'];

            $orden->total = $response->amount * 100;//Total pagado por cliente en CENTAVOS
            $orden->token_pago = $response->id;
            $orden->status_pedido_id = 7;#Procesando pago de spei
            $orden->skydropx_label_id = '0';

            #Se guarda la información del pago de openpay en una variable
            $spei_order = $res['data'];

            $spei_data = New SpeiData;

            $spei_data->speiable_type = Orden::class;
            $spei_data->type = $spei_order->payment_method->type;
            $spei_data->agreement = $spei_order->payment_method->agreement;
            $spei_data->bank = $spei_order->payment_method->bank;
            $spei_data->clabe = $spei_order->payment_method->clabe;
            $spei_data->name = $spei_order->payment_method->name;
            $spei_data->due_date = $spei_order->due_date;
            $spei_data->due_date_string = strftime('%d', strtotime($spei_order->due_date)).' de '.strftime('%B', strtotime($spei_order->due_date)). ' del '.strftime('%Y', strtotime($spei_order->due_date));

        }

        $orden->user_id = $user->id;
        $orden->direccion_id = $direccion->id;
        $orden->tipo_pago_id = $tipoPago->id;
        $orden->shipping_cost = $req->shipping_cost;
        $orden->fecha = date("Y-m-d H:i:s");
        // $orden->sub_total = $response->amount;

        $orden->save();

        #Se terminan de guardar los datos de spei
        if ( $tipoPago->descripcion == 'SPEI' ) {

            $spei_data->speiable_id = $orden->id;

            $spei_data->save();
        }

        foreach ($req->articulos as $articulo) {
            $det = New OrdenDetalle;

            $art = Articulo::find($articulo['id']);

            // $art = Articulo::find(7);
            // dd($art);

            if ( $art ) { $det->foto = $art->imagen; }

            $det->orden_id = $orden->id;
            $det->articulo_id = $articulo['id'];
            $det->articulo = $articulo['articulo'];
            $det->cantidad = $articulo['cantidad'];
            $det->precio_u = $articulo['precio_u'];
            $det->total = $articulo['cantidad'] * $articulo['precio_u'];

            $det->save();
        }

        $this->changeArticleStock($products_data['pro_array'], 'decrement');

        // $art = Articulo::find(7);

        $request_uri = "{$this->uri}v1/shipments";
        $request_uri2 = "{$this->uri}v1/labels";
        $client = new Client();
        $action = "POST";
        $title = "/quotations"; // Shipments

        $dataToSend = [
            "address_from" => [
                "province" => "Jalisco",
                "city" => "Zapopan",
                "name" => "Edgard Vargas",
                "zip" => "45134",
                "country" => "MX",
                "address1" => "Avenida Angel Leaño #3056",
                "company" => "Bridgestudio",
                "address2" => "Avenida Juan Gil Preciado",
                "phone" => "3312948789",
                "email" => "edgard@bridgestudio.mx"
            ],
            "parcels" => [
                ["weight" => $req->weight,
                "distance_unit" => "CM",
                "mass_unit" => "KG",
                "height" => 30,
                "width" => 30,
                "length" => 30]
            ],
            "address_to" => [
                "province" => $direccion->estado,
                "city" => $direccion->ciudad,
                "name" => $user->fullname,
                "zip" => $direccion->codigo_postal,
                "country" => $direccion->pais,
                "address1" => $direccion->calle,
                "company" => "-",
                "address2" => $direccion->colonia,
                "phone" => $user->phone,
                "email" => $user->email,
                "reference" => $direccion->referencias,
                "contents" => "Producto con embalaje"
            ],
            "consignment_note_class_code" => "53131600",
            "consignment_note_packaging_code" => "1H1",
            "carriers" => [
                [ "name" => "Estafeta" ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Token token=QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t',
            'Content-Type' => 'application/json',
        ])->post( $request_uri, $dataToSend);

        $data = json_decode($response->getBody());
        $status_code = $response->getStatusCode();

        $rate_id = (int)$data->data->relationships->rates->data[0]->id;

        $dataToSend2 = [
            "rate_id" => $rate_id,
            "label_format" => "pdf"
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Token token=QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t',
            'Content-Type' => 'application/json',
        ])->post( $request_uri2, $dataToSend2);

        $data2 = json_decode($response->getBody());

        DB::table('skypdrop_labels')->insert([
            'order_id' => $orden->id,
            'data' => json_encode($data2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $skypdropx_label = DB::table('skypdrop_labels')->where('order_id', $orden->id)->get()->first();

        // dd($skypdropx_label->id);

        $orden->skydropx_label_id = $skypdropx_label->id;
        $orden->save();

        // return response()->json(['data' => $data], 200);

        return response(['msg' => 'Orden procesada correctamente', 'status' => 'success', 'data' => $orden->load(['status', 'direccion', 'tipo_pago', 'detalles', 'spei_data'])], 200);
    }

    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function changeStatusOrder(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $order = Orden::where('user_id', $user->id)->where('id', $req->order_id)->where('tipo_pago_id', 3)->first();

        if (! $order ) { return response(['msg' => 'ID de orden inválido', 'status' => 'error'], 200); }

        if ( $req->status == 'success' ) {

            $order->token_pago = $req->token_pago;
            $order->paypal_user_id = $req->paypal_user_id;
            $order->status_pedido_id = 1;

            $order->save();

            return response(['msg' => 'Status de orden modificada', 'status' => 'success', 'data' => $order], 200);

        } else if ( $req->status == 'error' ) {

            $this->changeArticleStock($order->detalles->toArray(), 'increment');

            $order->delete();

            return response(['msg' => 'Pedido eliminado por falta de pago, vuelva a intentarlo', 'status' => 'success'], 200);

        }

        return response(['msg' => 'Proporcione un status válido para continuar', 'status' => 'success'], 200);
    }

    /**
     * Updates the player id for onesignal
     *
     * @return json
     */
    public function updatePlayerid(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'Usuario no encontrado.', 'status' => 'error'], 200); }

        $user->player_id = $req->player_id;

        $user->save();

        return response(['msg' => 'Player ID modificado con éxito', 'status' => 'success'], 200);
    }

    /**
     * Display the banners
     *
     * @return \Illuminate\Http\Response
     */
    public function banners(Request $req)
    {
        if ( $req->has('type') ) { $data = Banner::where('type', $req->type)->get(); }
        else { $data = Banner::all(); }

        if ( count( $data ) ) {
            return response(['msg' => 'Banners enlistados a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay banners por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Get the faqs
     *
     * @return \Illuminate\Http\Response
     */
    public function faqs()
    {
        $data = Faq::all();

        if ( count( $data ) ) {
            return response(['msg' => 'Preguntas frecuentes enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay preguntas frecuentes por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Get the categories
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $data = Categoria::all();

        if ( count( $data ) ) {
            return response(['msg' => 'Categorías enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay categorías por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Get terms and conditions
     *
     * @return view mail
     */
    public function getLegalInfo(Request $req)
    {
        $faqs = Faq::all();
        $terminos = Info::where('tipo', 'terminos')->first();
        $manual = Info::where('tipo', 'manual')->first();

        return response(['msg' => 'Información legal mostrada a continuación', 'status' => 'success', 'data' => ['faqs' => $faqs, 'terminos' => $terminos, 'manual' => $manual]], 200);
    }

    /**
     * Set the webhook for spei
     *
     * @return view mail
     */
    public function speiWebhook(Request $req)
    {
        $log = New Log;

        $log->contenido = json_encode($req->all());

        $log->save();

        #Field type is defined on request
        $type = ( $req->type ? $req->type : null );

        #Transaction object exist
        $transaction = ( $req->transaction ? $req->transaction : null );

        #Spei payment was accepted
        if ( $type && $type == 'charged.succeeded' ) {

            if ( $transaction && $transaction['transaction_type'] == 'charge' && $transaction['status'] == 'completed' ) {
                
                #Intenta buscar un registro de ORDEN con el id de pago
                $updatableItem = Orden::where('token_pago', $transaction['id'])->first();

                #Si no encuentra una orden, busca un registro de PEDIDO con el id de pago
                if (! $updatableItem ) {
                    $updatableItem = Pedido::where('token_orden', $transaction['id'])->first();
                }

                if ( $updatableItem ) {
                    $updatableItem->status_pedido_id = 1;

                    $updatableItem->save();
                    // dd($updatableItem);
                }
            }

            
        } elseif ( $type && $type == 'payout.failed' ) {
            #Spei payment was declined
            
        } else {
            /*Falta ver qué haría en el else, probablemente nada*/
        }
        
        return response(['msg' => 'Webhook validado correctamente', 'status' => 'success'], 200);
    }
}
