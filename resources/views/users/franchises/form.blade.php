@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-40 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum">
                            <li class="breadcrumb-item"><a href="javascript:;">Usuarios</a></li>
                            <li class="breadcrumb-item "><a href="{{url('usuarios/franquiciatarios')}}">Franquiciatarios </a></li>
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
                        <h2 class="">Ingresa los datos del franquiciatario</h2>
                    </div>
                    {{-- @if( $item )
                        <div class="text-center">
                            <img class="profile-img-form" src="{{asset($item->photo)}}">
                        </div>
                    @endif --}}
                    <div class="card-body">
                        <form id="form-data" action="{{url('usuarios/franquiciatarios/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="text-center">
                                <label class="avatar-input">
                                    <span class="avatar avatar-xxl">
                                        <img src="{{asset($item->photo)}}" alt="..." class="avatar-img avatar-profile-img rounded-circle">
                                        <span class="avatar-input-icon rounded-circle"><i class="mdi mdi-upload mdi-24px"></i></span>
                                    </span>
                                    <input type="file" name="avatar" class="avatar-file-picker file image" data-target="avatar-profile-img" data-msg="Foto de perfil">
                                </label>
                            </div>
                            <div class="form-row">
                                <div class="form-group floating-label" style="display: none;">
                                    <label>ID</label>
                                    <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                                </div>
                                <div class="form-group floating-label col-md-12">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control not-empty" name="fullname" value="{{$item ? $item->fullname : ''}}" placeholder="Nombre completo del franquiciatario" data-msg="Nombre completo">
                                </div>
                                <div class="form-group floating-label col-md-6">
                                    <label>Correo</label>
                                    <input type="email" class="form-control not-empty" name="email" value="{{$item ? $item->email : ''}}" placeholder="Correo" data-msg="Correo">
                                </div>
                                <div class="form-group floating-label col-md-6">
                                    <label>Contraseña</label>
                                    <input type="text" class="form-control pass-font {{$item ? '' : 'not-empty'}}" name="password" placeholder="Contraseña" data-msg="Contraseña">
                                </div>
                                <div class="form-group floating-label col-md-12 show-label">
                                    <label class="no-mar">Sucursal</label>
                                    <select class="form-control not-empty" name="id_sucursales" placeholder="Sucursal" data-msg="Sucursal">
                                        <option value="0" disabled selected>Seleccione una opción</option>
                                        @if( $item )
                                            @foreach($sucursales as $sucursal)
                                                <option value="{{$sucursal->id}}" {{$item->id_sucursales == $sucursal->id ? 'selected' : ''}}>{{$sucursal->nombre_sucursal}}</option>
                                            @endforeach
                                        @else
                                            @foreach($sucursales as $sucursal)
                                                <option value="{{$sucursal->id}}">{{$sucursal->nombre_sucursal}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group m-t-15">
                                <a href="{{url('usuarios/franquiciatarios')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
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