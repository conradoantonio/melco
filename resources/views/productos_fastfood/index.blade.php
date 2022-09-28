@extends('layouts.main')

@section('content')
@include('vistas_generales.import')
@include('productos_fastfood.modal')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>{{$title}}</h1>
                    <p class="opacity-75">
                        En este módulo podrá filtrar productos por categoría, recuerde que estos registros se dan de alta manualmente desde este panel administrativo.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("productos/fastfood")}}" data-refresh="table" data-el-loader="card">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            {{-- Table --}}
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Lista de productos</h2>
                        <div class="card-controls">
                            {{-- <a href="#" class="js-card-refresh icon"> </a> --}}
                            <a href="javascript:;" class="icon refresh-content"><i class="mdi mdi-refresh"></i> </a>
                            <a href="{{url('productos/fastfood/form')}}"><button class="btn btn-primary" type="button">Nuevo producto</button></a>
                            <a href="javascript:;" class="btn btn-secondary import-excel" data-toggle="modal" data-target="#modal-excel" data-fields='"clave, descripción, precio, foto"'> <i class="fa fa-file" aria-hidden="true"></i> Importar</a>
                            @if( auth()->user()->id_role == 1 )
                            <a href="javascript:;" class="btn btn-dark filter-rows">Filtrar</a>
                            @endif
                            <a href="javascript:;" class="btn btn-info export-rows">Exportar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row m-b-20">
                            @if( auth()->user()->id_role == 1 )
                                <div class="col-md-3 my-auto">
                                    <h4 class="m-0">Filtros</h4>
                                </div>
                                <div class="col-md-9 text-right my-auto filter-section">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <div class="no-pad text-left">
                                            <select class="form-control select2" name="id_categorias">
                                                <option value="">Categoría</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{$categoria->id}}">{{$categoria->nombre_categoria}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="no-pad text-left">
                                            <select class="form-control select2" name="id_sucursal">
                                                <option value="">Sucursal</option>
                                                @foreach($sucursales as $sucursal)
                                                    <option value="{{$sucursal->id}}">{{$sucursal->nombre_sucursal}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive rows-container">
                            @include('productos_fastfood.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    //Modal details
    $('body').delegate('.change-stock-row', 'click', function() {
        id = $(this).data('row-id');
        stock_sucs = $(this).data('row-franchises');
        var status = "{{ ($sucursales) }}";
        $("div#change-stock select[name='id_sucursales']").children('option').remove();
        $("div#change-stock input[name='stock']").val(0);
        $("div#change-stock input[name='id']").val(id);//Id del producto a cambiar stock

        if ( stock_sucs ) {
            $("div#change-stock select[name='id_sucursales']").append('<option value="0" disabled selected>Seleccione una opción</option>');
            $.each(stock_sucs, function(key, value) {
                //console.log( value, value.id );
                $("div#change-stock select[name='id_sucursales']").append('<option data-stock="'+(value.pivot ? ( value.pivot.cantidad ? parseInt(value.pivot.cantidad) : 0) : 0)+'" value="'+value.id+'">'+value.nombre_sucursal+'</option>');
            });
        }

        $('div#change-stock').modal();
    });

    $("div#change-stock select[name='id_sucursales']").on('change', function () {
        option = $(this).children("option:selected").data('stock');
        $("div#change-stock input[name='stock']").val(parseInt(option));
    });

    //Modal details
    $('body').delegate('.info-row', 'click', function() {
        id = $(this).data('row-id');
        url = $('div.general-info').data('url');
        var config = {
            "id"        : id,
            "modal_id"  : "modal-info",
            "route"     : url.concat('/show-info'),
            "callback"  : 'load_info',
            "keepModal" : true,
        }
        loadingMessage('Cargando...');
        ajaxSimple(config);
    });

    //Fill order data
    function load_info(response, config)
    {
        $('div#'+config.modal_id).modal();
        $('.ul-imagenes').addClass('d-none');
        $('.ul-sucursales').addClass('d-none');
        
        //General data
        fill_text(response, null, 'producto_');
        $('img.producto_photo').attr('src', baseUrl.concat('/'+response.imagen_principal));

        //Sucursales
        if ( response.sucursales ) {
            $("table.sucursales tbody").children().remove();
            $('.ul-sucursales').removeClass('d-none');
            items = response.sucursales;
            for ( var key in items ) {
                if ( items.hasOwnProperty( key ) ) {
                    $("table.sucursales tbody").append(
                        '<tr class="" id="">'+
                            '<td class="table-bordered">'+items[key].nombre_sucursal+'</td>'+
                            '<td class="table-bordered">'+(items[key].pivot.cantidad ? items[key].pivot.cantidad : 0)+'</td>'+
                        '</tr>'
                    );
                }
            }

            $("table.sucursales").parent('div').addClass('table-responsive');
        }
        
        //Imágenes
        if ( response.imagenes.length ) {
            $('.ul-imagenes .image-list').children().remove();
            $('.ul-imagenes').removeClass('d-none');
            items = response.imagenes;
            for ( var key in items ) {
                if ( items.hasOwnProperty( key ) ) {
                    $('.ul-imagenes .image-list').append(
                        '<div class="col-md-4">'+
                            /*'<a href="'+baseUrl.concat('/'+items[key].imagen)+'" data-lightbox="preview" data-title="Imágen num. '+( parseFloat(key) + 1 )+'">'+*/
                                '<img src="'+baseUrl.concat('/'+items[key].imagen)+'" class="img-thumbnail m-b-15 property-img" alt="Imágen num. '+( parseFloat(key) + 1 )+'" />'+
                            /*'</a>'+*/
                        '</div>'
                    )
                }
            }
        }
    }
</script>
@endsection