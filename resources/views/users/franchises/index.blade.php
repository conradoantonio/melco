@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Franquiciatarios</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar los usuarios franquiciatarios.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("usuarios/franquiciatarios")}}" data-refresh="table" data-el-loader="refreshable">
                    
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
                        <h2 class="">Lista de franquiciatarios</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="icon refresh-content"><i class="mdi mdi-refresh"></i> </a>
                            <a href="{{url('usuarios/franquiciatarios/form')}}"><button class="btn btn-dark" type="button">Nuevo usuario</button></a>
                            <a href="javascript:;" class="btn btn-success filter-rows">Filtrar</a>
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
                                        <select class="form-control" name="sucursal_id">
                                            <option value="">Sucursal</option>
                                            @foreach($sucursales as $sucursal)
                                                <option value="{{$sucursal->id}}">{{$sucursal->nombre_sucursal}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad">
                                        <select class="form-control" name="status">
                                            <option value="">Status</option>
                                            <option value="0">Deshabilitado</option>
                                            <option value="1">Activo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive rows-container">
                            @include('users.franchises.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection