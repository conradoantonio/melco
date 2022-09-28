<div class="container pull-up" id="printableArea">
    <div class="row">
        <div class="col-md-12 m-b-40">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="avatar avatar-sm">
                                <img src="{{ asset($item->user->photo)}}" class="avatar-img avatar-sm rounded-circle" alt="user-image">
                            </div>
                            <address class="m-t-10">
                                Cliente<br>
                                <span class="h4 font-primary"> {{$item->user->fullname}}</span>
                            </address>
                            @if( $item->direccion )
                            <div>
                                Dirección de entrega<br>
                                <span class="font-primary"> Calle: {{$item->direccion->calle}}</span> <br>
                                <span class="font-primary"> Colonia: {{$item->direccion->colonia}} {{$item->direccion->codigo_postal ?? null}}</span> <br>
                                <span class="font-primary"> Ciudad: {{($item->direccion->estado ?? null).', '.($item->direccion->ciudad ?? null)}}</span> <br>
                                <span class="font-primary"> Referencias: {{$item->direccion->referencias}}</span> <br>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6 text-right my-auto">
                            <h1 class="font-primary">Orden #{{$item->id}}</h1>
                            <div class="">Estatus: {!!'<span class="badge badge-soft-'.$item->status->clase.'">'.$item->status->descripcion.'</span>'!!}</div>
                            <div class="">Fecha de orden: {{$item->fecha_formateada}}</div>
                        </div>
                         <div class="col-md-12 text-right my-auto">
                            <h2 class="font-primary">Guía: #{{$skypdropx_label->data->attributes->tracking_number}}</h2>
                            <a class="btn btn-md btn-primary" target="_blank" href="{{ $skypdropx_label->data->attributes->label_url }}">Descargar Guía</a>
                        </div>
                    </div>
                    @if ( $item->detalles )
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
                                    @if( count($item->detalles) )
                                        @foreach($item->detalles as $producto)
                                            <tr>
                                                <td class="">
                                                    <p class="text-black m-0">{{$producto->articulo}}</p>
                                                </td>
                                                <td class="text-center">X{{$producto->cantidad}}</td>
                                                <td class="text-center">${{$producto->precio_u / 100}} MXN</td>
                                                <td class="text-right">${{$producto->total / 100}} MXN</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">
                                            Costo de envío
                                        </td>
                                        <td class="text-right">
                                            ${{$item->shipping_cost}} MXN
                                        </td>
                                    </tr>
                                    @php
                                        $total = $item->shipping_cost + $item->total;
                                    @endphp
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">
                                            Total
                                        </td>
                                        <td class="text-right">
                                            ${{$total}} MXN
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
                            Esta lista funciona como un historial de venta, por lo que los artículos aquí enlistados indican los datos de los mismos
                            al momento de su compra, si estos productos sufren cambios después de esto NO reflejarán aquí los nuevos datos.
                        </p>
                        <hr>
                        <div class="text-center opacity-75">
                            &copy; {{env('APP_NAME')}} 2021
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>