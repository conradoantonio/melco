@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-12 m-auto text-white p-t-40 p-b-90">
                    <h1>Guías Skydropx</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar las guías generadas por las compras de los usuarios desde la app de melwin
                    </p>
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
                        <h2 class="">Listado de guías generadas</h2>
                        <div class="card-controls">
                            {{-- <a href="#" class="js-card-refresh icon"> </a> --}}
                            <a href="javascript:;" class="icon refresh-content"><i class="mdi mdi-refresh"></i> </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('skydropx.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection