<div class="container pull-up" id="printableArea">
    <div class="row">
        <div class="col-md-12 m-b-40">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{ asset($item->user->photo)}}" width="60" class="rounded-circle" alt="">
                            <address class="m-t-10">
                                Cliente<br>
                                <span class="h4 font-primary"> {{$item->user->fullname}}</span> <br>
                                {{$item->proveedor->fullname}} <br>
                            </address>
                        </div>
                        <div class="col-md-6 text-right my-auto">
                            <h1 class="font-primary">Pedido #{{$item->id}}</h1>
                            <div class="">Status: {!!'<span class="badge badge-soft-'.$item->status->clase.'">'.$item->status->descripcion.'</span>'!!}</div>
                            <div class="">Fecha de pedido: {{$item->fecha_formateada}}</div>
                        </div>
                    </div>
                    @if ( $item->productos || $item->extras )
                        <div class="table-responsive products-row">
                            <table class="table m-t-50">
                                <thead>
                                <tr>
                                    <th class="">Nombre de producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Precio unitario</th>
                                    <th class="text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if( count($item->productos) )
                                        @foreach($item->productos as $producto)
                                            <tr>
                                                <td class="">
                                                    <p class="text-black m-0">{{$producto->nombre_producto}}</p>
                                                </td>
                                                <td class="text-center">X{{$producto->cantidad}}</td>
                                                <td class="text-center">${{$producto->precio_u}} MXN</td>
                                                <td class="text-right">${{$producto->total}} MXN</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">
                                            Total
                                        </td>
                                        <td class="text-right">
                                            ${{number_format($item->total)}} MXN
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if ( $item->cancelacion )
                        <div class="p-t-10 p-b-10 m-t-30 m-b-30 bg-light">
                            <p class="text-mute" style="margin-bottom: 0px;">
                                <strong>Cancelado por:</strong> {{$item->cancelacion->user->fullname}} <br>

                                <strong>Motivo:</strong> {{$item->cancelacion->comentario}} <br>

                                <strong>Status de reembolso:</strong>
                                @if( $item->cancelacion->status ==  0 )
                                    No reembolsable <br>
                                @elseif( $item->cancelacion->status ==  1 )
                                    Reembolsable <br>
                                @elseif( $item->cancelacion->status ==  2 )
                                    Reembolsado<br>
                                @elseif( $item->cancelacion->status ==  3 )
                                    No reembolsado<br>
                                @endif

                                @if( $item->cancelacion->respuesta )
                                    <strong>Réplica a rembolso:</strong> {{$item->cancelacion->respuesta}} <br>
                                @endif
                            </p>
                        </div>
                    @endif
                    <div class="p-t-10 p-b-20">
                        <p class="text-muted">
                            <strong>Nota:</strong>
                            Esta lista funciona como un historial de venta, por lo que los productos y porciones extras aquí enlistados indican los datos de los mismos
                            al momento de su compra, si estos productos sufren cambios después de esto NO reflejará los nuevos datos.
                        </p>
                        <hr>
                        <div class="text-center opacity-75">
                            &copy; Sinai 2019
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>