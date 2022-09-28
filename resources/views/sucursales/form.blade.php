@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Sucursales</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('sucursales/')}}"></a>Formulario</li>
                        </ol>
                    </nav>
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
                        <h2 class="">Ingrese los datos de la sucursales</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('sucursales/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="form-group floating-label" style="display: none;">
                                <label>ID</label>
                                <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                            </div>
                            <div class="form-group floating-label">
                                <label>Nombre</label>
                                <input type="text" class="form-control not-empty" name="nombre_sucursal" value="{{$item ? $item->nombre_sucursal : ''}}" placeholder="Nombre de la sucursal" data-msg="Nombre">
                            </div>
                            <div class="form-group floating-label">
                                <label>Código postal</label>
                                <input type="text" class="form-control not-empty" name="cod_postal" value="{{$item ? $item->cod_postal : ''}}" placeholder="Código postal de la sucursal" data-msg="Código postal">
                            </div>
                            <div id="mapa_detalles" class="form-group">
                                <div class="form-group floating-label">
                                    <label>Dirección</label>
                                    <input type="text" class="form-control search-box not-empty" name="search-box" id="search-box" value="{{$item ? $item->direccion : ''}}" placeholder="Dirección de la sucursal" data-msg="Dirección">
                                </div>
                            </div>
                             
                            <div id="map" class="z-depth-1 center-align valign-wrapper" style="height: 350px;width: 100%">
                                <i class="fa fa-spin fa-spinner fa-2x valign-wrapper" style="margin: auto;"></i>
                            </div>

                            <div class="form-group floating-label" style="display: none;">
                                <label>Latitud</label>
                                <input type="text" class="form-control not-empty" id="latitude" name="latitude" value="{{$item ? $item->latitud : ''}}" placeholder="Latitud" data-msg="Latitud">
                            </div>

                            <div class="form-group floating-label" style="display: none;">
                                <label>Latitud</label>
                                <input type="text" class="form-control not-empty" id="longitude" name="longitude" value="{{$item ? $item->longitud : ''}}" placeholder="Longitud" data-msg="Longitud">
                            </div>
                            <div class="form-group m-t-15">
                                <a href="{{url('sucursales')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                                <button type="submit" class="btn btn-success save">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('js/g-maps.js') }}"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBj6fY5sVLxsS7FswsQt_n6Oy1XRyTXxdA&callback=initMap&libraries=places"></script>
@endsection