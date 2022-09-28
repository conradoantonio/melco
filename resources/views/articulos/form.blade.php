@extends('layouts.main')
@section('content')
@include('vistas_generales.upload-images')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Artículos</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90 general-info" data-url="{{url("articulos")}}" data-refresh="galery" data-main-id="{{$item ? $item->id : ''}}">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('articulos')}}"></a>Formulario</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container pull-up">
        <div class="row">
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Ingrese los datos del producto</h2>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-justified" id="myTab3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="form-tab" data-toggle="tab" href="#form" role="tab" aria-controls="home" aria-selected="true">Formulario</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" id="galery-tab" data-toggle="tab" href="#galery" role="tab"  aria-selected="false">Galería</a>
                            </li> --}}
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="form" role="tabpanel" aria-labelledby="home-tab">
                                {{-- <div class="card-title m-t-10" style="font-size: 16px;">Escriba detalladamente las políticas de privacidad que se mostrarán en la aplicación</div> --}}
                                <form id="form-data" action="{{url('articulos/'.($item ? 'update' : 'save'))}}" class="m-t-20" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                                    <div class="text-center">
                                        <label class="avatar-input">
                                            <span class="avatar avatar-xxl">
                                                <img src="{{asset($item ? $item->imagen : 'img/no-image.png')}}" alt="..." class="avatar-img avatar-profile-img rounded-circle">
                                                <span class="avatar-input-icon rounded-circle"><i class="mdi mdi-upload mdi-24px"></i></span>
                                            </span>
                                            <input type="file" name="avatar" class="avatar-file-picker file image" data-target="avatar-profile-img" data-msg="Foto de perfil">
                                        </label>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6 d-none">
                                            <label>ID rtículo</label>
                                            <input type="text" class="form-control" readonly name="id" value="{{$item ? $item->id : ''}}" placeholder="ID de artículo">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Nombre</label>
                                            <input type="text" class="form-control not-empty" name="nombre" value="{{$item ? $item->nombre : ''}}" placeholder="Nombre del producto" data-msg="Nombre del producto">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Precio</label>
                                            <input type="text" class="form-control not-empty numeric" name="precio" value="{{$item ? number_format($item->precio, 2, '.', '') : ''}}" placeholder="Dos decimales máximo" data-msg="Precio">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Stock</label>
                                            <input type="number" class="form-control not-empty numeric" name="stock" value="{{$item ? $item->stock : ''}}" placeholder="Stock" data-msg="Stock">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Peso del Producto (KG)</label>
                                            <input type="number" class="form-control not-empty numeric" name="weight" value="{{$item ? $item->weight : ''}}" placeholder="Peso" data-msg="Peso">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <input type="text" class="form-control not-empty" name="descripcion" value="{{$item ? $item->descripcion : ''}}" placeholder="Descripción" data-msg="Descripción">
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_articulo_id">Categoría</label>
                                        <select name="tipo_articulo_id" class="form-control not-empty select2" data-msg="Categoría">
                                            <option value="0" selected disabled>Seleccione una opción</option>
                                            @if( $item )
                                            @foreach($tipos as $tipo)
                                            <option value="{{$tipo->id}}" {{($item->tipo_articulo_id == $tipo->id ? 'selected' : '')}}>{{$tipo->nombre}}</option>
                                            @endforeach
                                            @else
                                            @foreach($tipos as $tipo)
                                            <option value="{{$tipo->id}}">{{$tipo->nombre}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" {{$item && $item->destacado ? 'checked' : ''}} class="custom-control-input" name="destacado" id="destacado">
                                            <label class="custom-control-label" for="destacado">¿Producto destacado?</label>
                                        </div>
                                    </div>
                                    <div class="form-group m-t-15">
                                        <a href="{{url('articulos')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                                        <button type="submit" class="btn btn-success save">Guardar</button>
                                    </div>
                                </form>
                            </div>
                            {{-- <div class="tab-pane fade" id="galery" role="tabpanel">
                                <div class="card-title m-t-10" style="font-size: 16px;">Seleccione imágenes para eliminarlas</div>
                                <div class="card-controls">
                                    <a href="javascript:;" class="btn btn-dark upload-content" data-refresh="galery" data-row-id="{{$item->id}}" data-route-action="{{url("productos/supermarket/upload-content")}}" data-rename="" data-reload-url="{{url('articulos/get-galery/'.$item->id)}}" data-path="img/articulos/galeria/{{$item->id}}" data-resize='{"width": 338, "height": 217}' data-toggle="modal" data-target="#modal-upload-content">Cargar fotos</a>
                                    <a href="javascript:;" class="btn btn-danger delete-content disabled" data-refresh="galery" data-route-action="{{url("articulos/delete-content")}}">Eliminar imágenes</a>
                                </div>
                                <div class="galery-collection galery-container row">
                                    @include('articulos.galery')
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
$(function() {
    //Set up the select 2 inputs
    $("select.porciones, select.sucursales").select2({
        closeOnSelect: false,
    });
});
//Verify if the button for delete multiple can be clickable
$("body").delegate(".check-multiple", "click", function() {
    if ($(this).is(":checked")) {
        id_photos.push($(this).data("row-id"));
    } else {
        var index = id_photos.indexOf($(this).data("row-id"));
        if (index > -1) {
            id_photos.splice(index, 1);
        }
    }
    $(".delete-content").attr("disabled", id_photos.length > 0 ? false : true);

    if (id_photos.length > 0) {
        $(".delete-content").removeClass("disabled");
    } else {
        $(".delete-content").addClass("disabled");
    }
});
$("body").delegate(".delete-content", "click", function() {
    var route = $("div.general-info").data("url") + "/delete-content";
    var refresh = $("div.general-info").data("refresh");
    var main_id = $("div.general-info").data("main-id");
    swal({
        title:
            "Se eliminarán " +
            id_photos.length +
            " imágenes, ¿Está seguro de continuar?",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"],
        dangerMode: true,
    })
        .then((accept) => {
            if (accept) {
                config = {
                    route: route,
                    id: main_id,
                    ids: id_photos,
                    /*'refresh'  : refresh,*/
                    callback: "fillGalery",
                };
                loadingMessage();
                ajaxSimple(config);
            }
        })
        .catch(swal.noop);
});
</script>
@endsection