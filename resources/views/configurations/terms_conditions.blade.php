@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Configuración</h1>
                    <p class="opacity-75">
                        Aquí podrá configurar el contenido mostrado en la aplicación.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("configuracion")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Políticas de privacidad</h2>
                    </div>
                    <div class="card-body">
                        <div class="card-title m-t-10" style="font-size: 16px;">Escriba detalladamente las políticas de privacidad que se mostrarán en la aplicación</div>
                        @include('configurations.forms.terms_and_conditions')
                        {{-- <p class="p-t-20 m-b-0">Tab Pill #4 ....</p>
                        <p class="opacity-75">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab alias aliquam atque
                            consequatur cupiditate deserunt eaque ex in inventore ipsam, modi necessitatibus
                            nobis officia praesentium quae quaerat quasi rerum sint.
                        </p>
                        <p class="opacity-75">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Blanditiis dolore
                            eligendi exercitationem, fugit in minus, modi nemo nesciunt praesentium
                            provident quidem quo, quos rem reprehenderit sunt tempore ullam. Officiis,
                            omnis.
                        </p>
                        <p class="opacity-75">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda dolorum
                            ducimus eveniet ex exercitationem id impedit necessitatibus quam repellat rerum
                            vero, vitae. At libero modi nesciunt non suscipit veniam voluptate.
                        </p> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
