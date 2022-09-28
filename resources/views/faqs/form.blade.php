@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Preguntas frecuentes</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('preguntas-frecuentes/')}}"></a>Formulario</li>
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
                        <h2 class="">Ingresa los datos de la pregunta frecuente</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('preguntas-frecuentes/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="form-group floating-label" style="display: none;">
                                <label>ID</label>
                                <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                            </div>
                            <div class="form-group floating-label">
                                <label>Pregunta</label>
                                <input type="text" class="form-control not-empty" name="pregunta" value="{{$item ? $item->pregunta : ''}}" placeholder="Pregunta" data-msg="Pregunta">
                            </div>
                            <div class="form-group floating-label">
                                <label>Respuesta</label>
                                <input type="text" class="form-control not-empty" name="respuesta" value="{{$item ? $item->respuesta : ''}}" placeholder="Respuesta" data-msg="Respuesta">
                            </div>
                            <a href="{{url('preguntas-frecuentes')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                            <button type="submit" class="btn btn-success save">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection