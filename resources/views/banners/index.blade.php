@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Banners</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar los banners de la app móvil.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("configuracion/banners")}}" data-refresh="table" data-el-loader="refreshable">
                    
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
                        <h2 class="">Lista de banners</h2>
                        <div class="card-controls">
                            <a href="{{url('configuracion/banners/form')}}"><button class="btn btn-dark" type="button">Agregar banner</button></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('banners.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">

</script>
@endsection