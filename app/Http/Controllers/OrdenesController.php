<?php

namespace App\Http\Controllers;

use Excel;

use \App\User;
use \App\Orden;
use \App\TipoPago;
use \App\Cancelacion;
use \App\OrderDetail;
use \App\StatusPedido;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

use DB;

class OrdenesController extends Controller
{
     // Production Credentials
    private $apiKey = "QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t";

    // Pre-Production URL
    private $uri = "https://api-demo.skydropx.com/";

    // Production URL
    // private $uri = "https://api.hotelbeds.com/";

    /**
     * Show the orders.
     *
     */
    public function index(Request $req)
    {
        $menu = $title = 'Órdenes';
        $reject = [6,7,8];
        $status = StatusPedido::whereNotIn('id', $reject)->get();
        $tiposPago = TipoPago::all();
        $items = Orden::filter_rows(auth()->user(), $reject);
        $total = Orden::whereIn('status_pedido_id', [1,2,3])->sum('total');

        if ( $req->ajax() ) {
            return view('ordenes.table', compact(['items', 'title']));
        }

        return view('ordenes.index', compact(['items', 'status', 'tiposPago', 'total', 'menu', 'title']));
    }

    /**
     * Show the orders acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $reject = [6,7,8];

        $items = Orden::filter_rows( auth()->user(), $reject, $req->tipo_pago_id, $req->status_pedido_id, $req->fecha_inicio, $req->fecha_fin);

        return view('ordenes.table', compact(['items']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function showInfo(Request $req)
    {
        // dd('Acá por ID');

        // $client = new \GuzzleHttp\Client();
        // $res = $client->request('GET', 'https://api-demo.skydropx.com/v1/labels/11607779', [
        //             'headers'        => [
        //             'Access-Control-Request-Method' => 'POST, GET, OPTIONS, DELETE',
        //             'Access-Control-Allow-Origin' => '*',
        //             'Access-Control-Allow-Headers' => 'x-requested-with, Content-Type',
        //             'Api-key' => $this->apiKey,
        //             'Accept' => 'application/json',
        //             'Content-Type' => 'application/json',
        //             'Accept-Encoding' => 'gzip',
        //             'Authorization' => 'Token token="QFkZqkFAu9RWrDPVbaWB94fHLZwIifss2Ds2lzFRV58t"'
        //     ]
        // ]);

        // $dataObject = new \stdClass();
        // $dataObject->data = json_decode($res->getBody());
        // $dataObject->status_code = $res->getStatusCode();
        // $dataObject->header = $res->getHeader('content-type')[0];

        // // dd($dataObject->data);
        // $orderData = $dataObject->data->data;

        // dd($orderData);

        $menu = 'Órdenes';
        $title = 'Detalle orden';

        $item = Orden::where('id', $req->id)->first();

        if (! $item ) { return view('errors.404'); }

        $time = $item->fecha;

        $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time))/*. ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.'*/;

        $skypdropx_label = DB::table('skypdrop_labels')->where('order_id', $item->id)->get()->first();
        $skypdropx_label->data = json_decode($skypdropx_label->data);

        // dd($skypdropx_label);
        
        return view('ordenes.details', compact(['item', 'menu', 'title', 'skypdropx_label']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function getPrintable(Request $req)
    {
        $item = Orden::where('id', $req->id)->first();
        if (! $item ) { return view('errors.404'); }

        $time = $item->fecha;

        $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time))/*. ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.'*/;
        
        return view('ordenes.printable', compact(['item']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function sendOrderToCustomer(Request $req)
    {
        $item = Orden::find($req->pedido_id);

        if (! $item ) { return response(['status' => 'error', 'msg' => 'ID de pedido inválido'], 404); }
        
        $params = array();

        $params['view'] = 'mails.order_details';
        $params['subject'] = 'Detalles de tu pedido #'.$item->id;
        $params['item'] = $item;
        $params['email'] = $item->user->email;

        $this->f_mail( $params );
        
        return response(['status' => 'success', 'msg' => 'Correo enviado'], 200);
    }

    /**
     * Export the orders to excel according to the filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function export( Request $req )
    {
    	#Quitar el 5 en un futuro
        #$reject = $req->reject ? [6,7,8] : null;
        $reject = [6,7,8];

        $items = Orden::filter_rows( auth()->user(), $reject, $req->tipo_pago_id, $req->status_pedido_id, $req->fecha_inicio, $req->fecha_fin);
        
        $rows = array();

        foreach ( $items as $item ) {
            $rows [] = [
                'No. Orden' => '#'.$item->id,
                'Status de pedido' => $item->status ? $item->status->descripcion : 'Desconocido',
                'Cliente' => $item->user ? $item->user->fullname : 'Eliminado',
                'Método de pago' => $item->metodoPago ? $item->metodoPago->descripcion : 'Desconocido',
                'Subtotal' => '$'.$item->sub_total.' MXN',
                'Total' => '$'.$item->total.' MXN',
                'Fecha de pedido' => strftime('%d', strtotime($item->fecha)).' de '.strftime('%B', strtotime($item->fecha)). ' del '.strftime('%Y', strtotime($item->fecha)),
            ];
        }

        Excel::create('Lista de órdenes', function($excel) use($rows) {
            $excel->sheet('Sheet 1', function($sheet) use($rows) {
                $sheet->cells('A:F', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:F1', function($cells) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray($rows);
            });
        })->export('xlsx');
    }

    /**
     * Change the status of an order
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $req)
    {
        $item = Orden::find( $req->id );
        $status = StatusPedido::find( $req->status_id );

        if (! $item ) { return response(['msg' => 'ID de orden inválido', 'status' => 'error'], 404); }
        if (! $status ) { return response(['msg' => 'ID de status inválido', 'status' => 'error'], 404); }

        #Nuevo status debe ser mayor al actual y ser diferente a cancelado
        if ( $status->id != 5 && ( $status->id > $item->status->id ) ) {
            $item->status_pedido_id = $status->id;

            $item->save();

            $this->sendNotification(2, $this->app_id, $this->app_key, $this->app_icon, $status->descripcion, 'Consulta los detalles de tu orden en la sección de órdenes', null, null, ['origin' => 'System', 'status' => $status], [$item->user_id]);

            return response(['msg' => 'Status de orden modificado exitósamente', 'status' => 'success', 'url' => url('ordenes')], 200);
        }
        return response(['msg' => 'El status seleccionado no puede ser aplicado a esta orden', 'status' => 'error'], 400); 
    }
    
    /**
     * Cancell an order. Only order with status "creado" and "preparado" can be cancelled
     *
     * @return \Illuminate\Http\Response
     */
    public function cancellOrder(Request $req)
    {
        $item = Orden::where('id', $req->row_id)->whereIn('status_pedido_id', [1,2])->first();
        $user = auth()->user();
        $users_id = [];

        if (! $item ) { return response(['msg' => 'ID de pedido inválido o ya no puede cancelarse', 'status' => 'error'], 404); }

        $item->status_pedido_id = 4;#Cancelled

        $item->save();#Updates order status

        #Let's save cancellation
        $cancell = New Cancelacion;

        $cancell->cancelable_id = $item->id;
        $cancell->cancelable_type = Orden::class;
        $cancell->user_id = $user->id;
        $cancell->comentario = $req->comment;
        #Only card will be refundable
        $cancell->status = $item->tipo_pago_id == 1 ? 1 : 0;

        $cancell->save();

        #send notification to customer
        $item->user ? $users_id[] = $item->user->id : '';

        if ( count($users_id) ) {
            $this->sendNotification(2, $this->app_id, $this->app_key, $this->app_icon, "Orden cancelada!", "Tu orden #".$item->id." ha sido cancelada", null, null, ['origin' => 'System'], $users_id);
        }

        $item = Orden::where('id', $req->row_id)->with('cancelacion')->first();
        
        return response(['msg' => 'La orden ha sido cancelado corectamente', 'status' => 'success', 'url' => url('ordenes'), 'data' => ['item' => $item]], 200);
    }

    /**
     * Refund an order with openpay- Only orders with status "cancelado" and paid by card can be refunded.
     *
     * @return \Illuminate\Http\Response
     */
    public function refundOrder(Request $req)
    {
        $item = Orden::where('id', $req->row_id)->where('tipo_pago_id', 1)->whereHas('cancelacion')->first();
        if (! $item ) { return response(['msg' => 'ID de servicio inválido', 'status' => 'error'], 404); }
        if ( $item->cancelacion && $item->cancelacion->status != 1 ) { return response(['msg' => 'Este servicio no puede reembolsarse', 'status' => 'error'], 404); }

        #Retrieve the customer
        $user = User::find( $item->id_users );
        if (! $user ) { return response(['msg' => 'No puede reembolsarse el servicio a este cliente', 'status' => 'error'], 404); }

        #If refund was approved, let's do it
        if ( $req->refund == 1 ) {
            $res = $this->refund($req, $user, $item, $item->total);
            
            if ( $res['status'] == 'success' ) {
                #Let's update his relation
                $item->cancelacion()->update([
                    'respuesta'=> $req->comment,
                    'status'=> 2,#Refunded
                ]);

                $item->status_pedido_id = 5;#Reembolsado

                $item->save();
                
                $item = Orden::where('id', $req->row_id)->with('cancelacion')->first();

                return response(['msg' => 'Orden reembolsado correctamente', 'status' => 'success', 'url' => url('ordenes'), 'data' => ['item' => $item]], 200);
            } else {
                #Something went wrong
                return response($res, 400);
            }
        } else {
            #Refund was denied
            $item->cancelacion()->update([
                'respuesta'=> $req->comment,
                'status'=> 3,#Rejected
            ]);
            
            $item = Orden::where('id', $req->row_id)->with('cancelacion')->first();

            return response(['msg' => 'El reembolso ha sido marcado como rechazado correctamente', 'status' => 'success', 'url' => url('ordenes'), 'data' => ['item' => $item]], 200);
        }
    }
}
