@extends('layouts.main')

@section('content')
@include('pedidos.modal')
<section class="admin-content">
    <div class="bg-dark m-b-30">
        <div class="container">
            <div class="row p-b-60 p-t-60">
                <div class="col-md-6 text-white p-b-30 general-info" data-url="{{url("pedidos")}}" data-refresh="table" data-el-loader="refreshable">
                    <div class="media">
                        <div class="avatar avatar mr-3">
                            <div class="avatar-title bg-success rounded-circle mdi mdi-receipt"></div>
                        </div>
                        <div class="media-body">
                            <h4 class="m-b-0">Sucursal: {{$item->proveedor->fullname}} </h4>
                            <div class="opacity-75">{{$item->proveedor->phone}}</div>
                            {{-- <p class="opacity-75">
                                ID de pedido: #{{$item->id}} <br>
                                Fecha de pedido: {{$item->fecha_formateada}}
                            </p> --}}
                            <div class="buttons">
                                <button class="btn mr-2 btn-outline-primary" id="printDiv" > <i class="mdi mdi-printer"></i>Imprimir</button>
                                <button class="btn mr-2 btn-outline-secondary send-order d-none" data-row-id="{{$item->id}}" > <i class="mdi mdi-email"></i>Enviar correo a cliente</button>
                                @if(! $item->cancelacion && $item->status->id < 3 )
                                    <button class="btn mr-2 btn-outline-danger cancel-row" data-row-id="{{$item->id}}" > <i class="mdi mdi-cancel"></i>Cancelar orden</button>
                                @elseif( $item->id_tipo_pago == 1 &&  $item->cancelacion && $item->cancelacion->status == 1 )
                                    <button class="btn mr-2 btn-outline-info refund-row" data-row-id="{{$item->id}}" > <i class="mdi mdi-cash-refund"></i>Reembolsar pedido</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="printable-area">
        @include('pedidos.printable')
    </div>
</section>
<script type="text/javascript">
    $('body').delegate('.send-order','click', function() {
        var route = baseUrl.concat('/enviar-orden');
        var pedido_id = $('div.general-info').data('row-id');
        swal({
            title: '¿Desea enviar los detalles del pedido al cliente?',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if ( accept ) {
                config = {
                    'route'     : route,
                    'pedido_id' : pedido_id,
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    function getPrintable(data, config) {
        var item = data.data['item'];
        var route = baseUrl.concat('/pedidos/get-printable/'+item.id);
        $('button.cancel-row, button.refund-row').remove();

        refreshContent(route, config.container_class);

        if (! item.cancelacion ) {
            console.log('pasa a colocar boton cancelar');
            $('div.buttons').append('<button class="btn mr-2 btn-outline-danger cancel-row" data-row-id="'+item.id+'" > <i class="mdi mdi-cancel"></i>Cancelar orden</button>');
        } else if( item.id_tipo_pago == 1 &&  item.cancelacion && item.cancelacion.status == 1 ) {
            console.log('pasa a colocar boton reembolsar');
            $('div.buttons').append('<button class="btn mr-2 btn-outline-info refund-row" data-row-id="'+item.id+'" > <i class="mdi mdi-cash-refund"></i>Reembolsar pedido</button>');
        }
    }
</script>
@endsection