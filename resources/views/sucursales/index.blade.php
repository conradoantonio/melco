@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Sucursales</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar las sucursales de la aplicación móvil.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("sucursales")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Lista de sucursales</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="icon refresh-content"><i class="mdi mdi-refresh"></i> </a>
                            <a href="{{url('sucursales/form')}}"><button class="btn btn-dark" type="button">Nueva sucursal</button></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('sucursales.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection