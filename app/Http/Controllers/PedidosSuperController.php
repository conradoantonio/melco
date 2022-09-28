<?php

namespace App\Http\Controllers;

use Excel;

use \App\User;
use \App\Pedido;
use \App\Sucursal;
use \App\Cancelacion;
use \App\OrderDetail;
use \App\StatusPedido;

use \App\Events\RefreshEvent;

use Illuminate\Http\Request;

class PedidosSuperController extends Controller
{
    /**
     * Show the orders.
     *
     */
    public function index(Request $req)
    {
        $menu = $title = 'Pedidos';
        $reject = [];
        $sucursales = User::where('role_id', 2)->get();
        $status = StatusPedido::whereNotIn('id', $reject)->get();
        $items = Pedido::all();
        #$items = Pedido::filter_rows(auth()->user(), $reject);

        if ( $req->ajax() ) {
            return view('pedidos_super.table', compact(['items', 'title']));
        }
        return view('pedidos_super.index', compact(['items', 'sucursales', 'status', 'menu', 'title']));
    }

    /**
     * Show the orders acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $items = Pedido::filter_rows( auth()->user(), [], 1, $req->status_id, $req->id_sucursal, $req->fecha_inicio, $req->fecha_fin);

        return view('pedidos_super.table', compact(['items']));
    }

    /**
     * Show the info of an order.
     *
     */
    public function showInfo(Request $req)
    {
        $menu = 'Pedidos';
        $title = 'Detalle pedido super market';

        $item = Pedido::where('id', $req->id)->first();
        if (! $item ) { return view('errors.404'); }

        $time = $item->fecha;

        $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time))/*. ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.'*/;
        
        return view('pedidos_super.details', compact(['item', 'menu', 'title']));
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
        
        return view('pedidos_super.printable', compact(['item']));
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
        $reject = $req->reject ? [5,6] : null;
        $items = Pedido::filter_rows(auth()->user(), $reject, 1, $req->status_id, $req->franchise_id, $req->start_date, $req->end_date);
        $rows = array();

        foreach ( $items as $item ) {
            $rows [] = [
                'No. Pedido' => '#'.$item->id,
                'Sucursal' => $item->sucursal ? $item->sucursal->nombre_sucursal : 'Sucursal eliminada',
                'Status de pedido' => $item->status ? $item->status->descripcion : 'Desconocido',
                'Cliente' => $item->user ? $item->user->fullname : 'Eliminado',
                'Método de pago' => $item->metodoPago ? $item->metodoPago->descripcion : 'Desconocido',
                'Subtotal' => '$'.$item->sub_total.' MXN',
                'Total' => '$'.$item->total.' MXN',
                'Fecha de pedido' => strftime('%d', strtotime($item->fecha)).' de '.strftime('%B', strtotime($item->fecha)). ' del '.strftime('%Y', strtotime($item->fecha)),
            ];
        }

        Excel::create('Lista de pedidos fastfood', function($excel) use($rows) {
            $excel->sheet('Sheet 1', function($sheet) use($rows) {
                $sheet->cells('A:H', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:H1', function($cells) {
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
            $item->id_status_pedido = $status->id;

            $item->save();

            $this->sendNotification(2, $this->app_id, $this->app_key, $this->app_icon, $status->descripcion, 'Consulta los detalles de tu pedido en la sección de pedidos', null, null, ['origin' => 'System', 'status' => $status], [@$item->user->id]);

            return response(['msg' => 'Status de pedido modificado exitósamente', 'status' => 'success', 'url' => url('pedidos/supermarket')], 200);
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
        $item = Pedido::where('id', $req->row_id)->whereIn('id_status_pedido', [1,2])->first();
        $user = auth()->user();
        $users_id = [];

        if (! $item ) { return response(['msg' => 'ID de pedido inválido o ya no puede cancelarse', 'status' => 'error'], 404); }

        $item->id_status_pedido = 5;#Cancelled

        $item->save();#Updates order status

        #Let's save cancellation
        $cancell = New Cancelacion;

        $cancell->id = $item->id;
        $cancell->user_id = $user->id;
        $cancell->comentario = $req->comment;
        #Only card will be refundable
        $cancell->status = $item->id_tipo_pago == 1 ? 1 : 0;

        $cancell->save();

        #send notification to customer
        $item->user ? $users_id[] = $item->user->id : '';

        if ( count($users_id) ) {
            $this->sendNotification(2, $this->app_id, $this->app_key, $this->app_icon, "¡Pedido cancelado!", "Tu orden #".$item->id." ha sido cancelada", null, null, ['origin' => 'System'], $users_id);
        }

        $item = Pedido::where('id', $req->row_id)->with('cancelacion')->first();
        
        return response(['msg' => 'El pedido ha sido cancelado corectamente', 'status' => 'success', 'url' => url('pedidos/supermarket'), 'data' => ['item' => $item]], 200);
    }

    /**
     * Refund an order with openpay- Only orders with status "cancelado" and paid by card can be refunded.
     *
     * @return \Illuminate\Http\Response
     */
    public function refundOrder(Request $req)
    {
        $item = Pedido::where('id', $req->row_id)->where('id_tipo_pago', 1)->whereHas('cancelacion')->first();
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

                $item->id_status_pedido = 6;#Reembolsado

                $item->save();
                
                $item = Pedido::where('id', $req->row_id)->with('cancelacion')->first();

                return response(['msg' => 'Pedido reembolsado correctamente', 'status' => 'success', 'url' => url('pedidos/supermarket'), 'data' => ['item' => $item]], 200);
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

            return response(['msg' => 'El reembolso ha sido marcado como rechazado correctamente', 'status' => 'success', 'url' => url('pedidos/supermarket'), 'data' => ['item' => $item]], 200);
        }
    }
}
