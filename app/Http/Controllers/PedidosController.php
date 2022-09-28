<?php

namespace App\Http\Controllers;

use Excel;

use \App\User;
use \App\Pedido;
use \App\Sucursal;
use \App\TipoPago;
use \App\Cancelacion;
use \App\OrderDetail;
use \App\StatusPedido;

use \App\Events\RefreshEvent;

use Illuminate\Http\Request;

class PedidosController extends Controller
{
    /**
     * Show the orders.
     *
     */
    public function index(Request $req)
    {
        $menu = $title = 'Pedidos';
        $reject = [6,7,8];
        
        $sucursales = User::where('role_id', 2)->get();
        $status = StatusPedido::whereNotIn('id', $reject)->get();
        $tiposPago = TipoPago::all();
        $items = Pedido::filter_rows(auth()->user(), $reject);
        $total = Pedido::whereIn('status_pedido_id', [1,2,3])->sum('total');

        if ( $req->ajax() ) {
            return view('pedidos.table', compact(['items', 'title']));
        }
        return view('pedidos.index', compact(['items', 'sucursales', 'tiposPago', 'total', 'status', 'menu', 'title']));
    }

    /**
     * Show the orders acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $reject = [6,7,8];

        $items = Pedido::filter_rows( auth()->user(), $reject, $req->tipo_pago_id, $req->status_pedido_id, $req->fecha_inicio, $req->fecha_fin);

        return view('pedidos.table', compact(['items']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function showInfo(Request $req)
    {
        $menu = 'Pedidos';
        $title = 'Detalle pedido';

        $item = Pedido::where('id', $req->id)->first();
        if (! $item ) { return view('errors.404'); }

        $time = $item->fecha;

        $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time))/*. ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.'*/;
        
        return view('pedidos.details', compact(['item', 'menu', 'title']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function getPrintable(Request $req)
    {
        $item = Pedido::where('id', $req->id)->first();
        if (! $item ) { return view('errors.404'); }

        $time = $item->fecha;

        $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time))/*. ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.'*/;
        
        return view('pedidos.printable', compact(['item']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function sendOrderToCustomer(Request $req)
    {
        $item = Pedido::find($req->pedido_id);

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
        $reject = [6,7,8];
        #$reject = $req->reject ? [6,7,8] : [];

        $items = Pedido::filter_rows( auth()->user(), $reject, $req->tipo_pago_id, $req->status_pedido_id, $req->fecha_inicio, $req->fecha_fin);
        $rows = array();

        foreach ( $items as $item ) {
            $rows [] = [
                'No. Pedido' => '#'.$item->id,
                'Status de pedido' => $item->status ? $item->status->descripcion : 'Desconocido',
                'Cliente' => $item->user ? $item->user->fullname : 'Eliminado',
                'Método de pago' => $item->metodoPago ? $item->metodoPago->descripcion : 'Desconocido',
                'Subtotal' => '$'.$item->sub_total.' MXN',
                'Total' => '$'.$item->total.' MXN',
                'Fecha de pedido' => strftime('%d', strtotime($item->fecha)).' de '.strftime('%B', strtotime($item->fecha)). ' del '.strftime('%Y', strtotime($item->fecha)),
            ];
        }

        Excel::create('Lista de pedidos', function($excel) use($rows) {
            $excel->sheet('Sheet 1', function($sheet) use($rows) {
                $sheet->cells('A:G', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:G1', function($cells) {
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
        $item = Pedido::find( $req->id );
        $status = StatusPedido::find( $req->status_id );

        if (! $item ) { return response(['msg' => 'ID de pedido inválido', 'status' => 'error'], 404); }
        if (! $status ) { return response(['msg' => 'ID de status inválido', 'status' => 'error'], 404); }

        #Nuevo status debe ser mayor al actual y ser diferente a cancelado
        if ( $status->id != 5 && ( $status->id > $item->status->id ) ) {
            $item->status_pedido_id = $status->id;

            $item->save();

            $this->sendNotification(2, $this->app_id, $this->app_key, $this->app_icon, $status->descripcion, 'Consulta los detalles de tu pedido en la sección de pedidos', null, null, ['origin' => 'System', 'status' => $status], [$item->user_id]);

            return response(['msg' => 'Status de pedido modificado exitósamente', 'status' => 'success', 'url' => url('pedidos')], 200);
        }
        return response(['msg' => 'El status seleccionado no puede ser aplicado a este pedido', 'status' => 'error'], 400); 
    }
    
    /**
     * Cancell an order. Only order with status "creado" and "preparado" can be cancelled
     *
     * @return \Illuminate\Http\Response
     */
    public function cancellOrder(Request $req)
    {
        $item = Pedido::where('id', $req->row_id)->whereIn('status_pedido_id', [1,2])->first();
        $user = auth()->user();
        $users_id = [];

        if (! $item ) { return response(['msg' => 'ID de pedido inválido o ya no puede cancelarse', 'status' => 'error'], 404); }

        $item->status_pedido_id = 4;#Cancelled

        $item->save();#Updates order status

        #Let's save cancellation
        $cancell = New Cancelacion;

        $cancell->cancelable_id = $item->id;
        $cancell->cancelable_type = Pedido::class;
        $cancell->user_id = $user->id;
        $cancell->comentario = $req->comment;
        #Only card will be refundable
        $cancell->status = $item->tipo_pago_id == 1 ? 1 : 0;

        $cancell->save();

        #send notification to customer
        $item->user ? $users_id[] = $item->user->id : '';

        if ( count($users_id) ) {
            $this->sendNotification(2, $this->app_id, $this->app_key, $this->app_icon, "¡Pedido cancelado!", "Tu orden #".$item->id." ha sido cancelada", null, null, ['origin' => 'System'], $users_id);
        }

        $item = Pedido::where('id', $req->row_id)->with('cancelacion')->first();
        
        return response(['msg' => 'El pedido ha sido cancelado corectamente', 'status' => 'success', 'url' => url('pedidos'), 'data' => ['item' => $item]], 200);
    }

    /**
     * Refund an order with openpay- Only orders with status "cancelado" and paid by card can be refunded.
     *
     * @return \Illuminate\Http\Response
     */
    public function refundOrder(Request $req)
    {
        $item = Pedido::where('id', $req->row_id)->where('tipo_pago_id', 1)->whereHas('cancelacion')->first();
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
                
                $item = Pedido::where('id', $req->row_id)->with('cancelacion')->first();

                return response(['msg' => 'Pedido reembolsado correctamente', 'status' => 'success', 'url' => url('pedidos'), 'data' => ['item' => $item]], 200);
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
            
            $item = Pedido::where('id', $req->row_id)->with('cancelacion')->first();

            return response(['msg' => 'El reembolso ha sido marcado como rechazado correctamente', 'status' => 'success', 'url' => url('pedidos'), 'data' => ['item' => $item]], 200);
        }
    }
}
