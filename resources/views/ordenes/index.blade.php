@extends('layouts.main')

@section('content')
@include('ordenes.modal')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Órdenes</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar los órdenes de artículos de tecnología, muebles etc., realizados desde la aplicación móvil.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("ordenes")}}" data-refresh="table" data-el-loader="refreshable">
                    <div class="rounded text-white bg-white-translucent">
                        <div class="p-all-15">
                            <div class="row">
                                <div class="col-md-12 my-2 m-md-0">
                                    <div class="text-overline opacity-75">Total de ingresos</div>
                                    <h3 class="m-0 text-success">${{number_format($total / 100)}} MXN</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            {{-- Table --}}
            <div class="col-lg-12 m-b-30">
                <div class="card refreshable">
                    <div class="card-header">
                        <h2 class="">Lista de órdenes</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="icon refresh-content"><i class="mdi mdi-refresh"></i> </a>
                            <a href="javascript:;" class="btn btn-dark filter-rows">Filtrar</a>
                            <a href="javascript:;" class="btn btn-info export-rows">Exportar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <div class="no-pad">
                                        <select class="form-control" name="status_pedido_id">
                                            <option value="">Status (Cualquiera)</option>
                                            @foreach($status as $stat)
                                                <option value="{{$stat->id}}">{{$stat->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad">
                                        <select class="form-control" name="tipo_pago_id">
                                            <option value="">Tipo de pago (Cualquiera)</option>
                                            @foreach($tiposPago as $tipo)
                                                <option value="{{$tipo->id}}">{{$tipo->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" placeholder="Fecha inicio">
                                    </div>
                                    <div class="no-pad">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" placeholder="Fecha fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive rows-container">
                            @include('ordenes.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    //Change order status
    $('body').delegate('.change-order-status', 'click', function() {
        $("form#form-change-order-status select[name='status_id']").children().remove();
        url = $('div.general-info').data('url');
        var status = "{{ ($status) }}";
        status = JSON.parse(status.replace(/&quot;/g,'"'));
        var reload_url = $(this).data('reload-url');
        var row_id = $(this).data('row-id')
        var row_status = $(this).data('row-status');

        $("form#form-change-order-status input[name='id']").val(row_id);

        if ( status ) {
            $("form#form-change-order-status select[name='status_id']").append('<option value="0" disabled selected>Seleccione una opción</option>');
            $.each(status, function(key, value) {
                console.log( row_status, value.id );
                if ( value.id < 4 && ( value.id > row_status ) ) {
                    $("form#form-change-order-status select[name='status_id']").append('<option value="'+value.id+'">'+value.descripcion+'</option>');
                }
            });
        }
        $('div#modal-change-order-status').modal();
    });
</script>
@endsection