@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-40 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum">
                            <li class="breadcrumb-item"><a href="javascript:;">Configuración</a></li>
                            <li class="breadcrumb-item "><a href="{{url('configuracion/banners')}}">Banners </a></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 m-auto text-white p-t-40 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page">Formulario</li>
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
                        <h2 class="">Ingresa la imagen del banner</h2>
                    </div>
                    {{-- @if( $item )
                        <div class="text-center">
                            <img class="profile-img-form" src="{{asset($item->img)}}">
                        </div>
                    @endif --}}
                    <div class="card-body">
                        <form id="form-data" action="{{url('configuracion/banners/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="form-row">
                                <div class="form-group floating-label" style="display: none;">
                                    <label>ID</label>
                                    <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                                </div>
                            </div>
                            <div class="alert alert-border-info alert-dismissible fade show" role="alert">
                                <div class="d-flex">
                                    <div class="icon">
                                        <i class="icon mdi mdi-alert-circle-outline"></i>
                                    </div>
                                    <div class="content">
                                        <strong>Nota:</strong> <br>
                                        - Los banners deben tener una medida de 1280 px X 256 px o un ratio similar para ser visualizada armónicamente en la app móvil.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- Foto del banner --}}
                            <div class="text-center">
                                <label class="avatar-input">
                                    <span class="avatar avatar-xxl">
                                        <img src="{{asset($item ? $item->img : 'img/no-image.png')}}" alt="..." class="avatar-img avatar-profile-img rounded-circle">
                                        <span class="avatar-input-icon rounded-circle"><i class="mdi mdi-upload mdi-24px"></i></span>
                                    </span>
                                    <input type="file" name="avatar" class="avatar-file-picker file image" data-target="avatar-profile-img" data-msg="Foto institución">
                                </label>
                            </div>
                            <div class="form-group m-t-15">
                                <a href="{{url('configuracion/banners')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                                <button type="submit" class="btn btn-success save">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection