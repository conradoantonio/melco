@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Notificaciones push</h1>
                    <p class="opacity-75">
                        En este módulo podrá enviar notificaciones push a los clientes de la plataforma, 
                        tenga en cuenta que los clientes que han desinstalado la aplicación NO recibirán notificación alguna de ningún tipo.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("notificaciones-push")}}" data-refresh="table" data-el-loader="refreshable">
                    <div class="rounded text-white bg-white-translucent">
                        <div class="p-all-15 text-right">
                            <div class="row">
                                <div class="col-md-12 my-2 m-md-0">
                                    <div class="text-overline opacity-75">Total de usuarios a notificar</div>
                                    <h3 class="m-0 text-success counter">{{count($customers)}}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <h2 class="">Formulario de notificaciones</h2>
                        <div class="card-controls">
                            {{-- Options --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row m-b-20">
                            <div class="col-md-12">
                                <div class="alert alert-border-info alert-dismissible fade show" role="alert">
                                    <div class="d-flex">
                                        <div class="icon">
                                            <i class="icon mdi mdi-alert-circle-outline"></i>
                                        </div>
                                        <div class="content">
                                            <strong>Instrucciones de uso:</strong> <br>
                                            - Complete los campos llamados fecha y hora para programar el momento en que debe enviarse la notificación, deje estos vacíos para enviarla inmediatamente.<br>
                                            - No se pueden programar notificaciones para ser enviadas en fechas u horarios que ya pasaron.<br>
                                            - Las notificaciones no pueden ser canceladas una vez sean programadas.<br>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('notificaciones.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $("#users_id").on("change", function (e) {
        if ( $( "select[name=type]" ).val() == 2 ) {
            $('.counter').text($("select[name='users_id[]'] option:selected").length);
        }
        console.log("change");
    });


    $('select[name=filter]').on('change', function (e) {
        var route = $('div.general-info').data('url')+'/filter';
        var filter = $(this).val();
        
        var config = {
            'filter'    : filter,
            'route'     : route,
            'callback'  : 'fillSelect',
        }

        loadingMessage();
        ajaxSimple(config);
    });

    function fillSelect(data, response) {
        $('select#users_id option').remove();

        $('select#users_id').append('<option value="0" disabled>Seleccionar usuarios</option>');
        
        data.data.forEach(function (option) {
            $('select#users_id').append('<option value="'+option.id+'">'+option.fullname+'</option>');
        });

        $('.counter').text(data.data.length);
    }

    {{-- If notification type changes, then show different inputs --}}
    $( "select[name=type]" ).change(function() {
        type = $('select#type').val();
        select = $('select#users_id');
        options = null;
        
        if (type == 2) {//Individual notification
            $('.users-content').removeClass('d-none');
            $('select#users_id').addClass('not-empty');
            $('.counter').text($("select[name='users_id[]'] option:selected").length);
        } else {
            $('.users-content').addClass('d-none');
            $('select#users_id').removeClass('not-empty');
            $('.counter').text($("select[name='users_id[]'] > option").length - 1);
        }
    });

    $('select#users_id').trigger('change.select2', function() {
        console.log('cambios extraños hay en mi');
    });

    //Send a request for multiple delete
    $('body').delegate('.delete-rows','click', function() {
        var route = $('div.general-info').data('url')+'/delete';
        var refresh = $('div.general-info').data('refresh');
        var ids_array = [];
        $("input.checkMultiple").each(function() {
            if($(this).is(':checked')) {
                ids_array.push($(this).parent().parent().siblings("td:nth-child(2)").text());
            }
        });
        if (ids_array.length > 0) {
            
            swal({
                title: 'Se dará de baja '+ids_array.length+' registro(s), ¿Está seguro de continuar?',
                icon: 'warning',
                buttons:["Cancelar", "Aceptar"],
                dangerMode: true,
            }).then((accept) => {
                if (accept) {
                    config = {
                        'route'     : route,
                        'ids'       : ids_array,
                        'refresh'   : refresh,
                    }
                    loadingMessage();
                    ajaxSimple(config);
                }
            }).catch(swal.noop);
        }
    });
</script>
@endsection