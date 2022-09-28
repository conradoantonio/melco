@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row p-b-60 p-t-60">
                <div class="col-lg-8 text-center mx-auto text-white p-b-30">
                    <div class="m-b-10">
                        <div class="avatar avatar-lg ">
                            <div class="avatar-title bg-info rounded-circle mdi mdi-settings"></div>
                        </div>
                    </div>
                    <h3>Configuración de usuario </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            <div class="col-lg-10 mx-auto mt-2">
               <div class="card py-3 m-b-30">
                   <div class="card-body">
                        @include('mi-perfil.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection