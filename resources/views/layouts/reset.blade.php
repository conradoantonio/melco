<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<title>Recuperar contraseña</title>
<link rel="icon" type="image/x-icon" href="{{ asset('/img/logo.png') }}"/>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png "sizes="16x16">
<link rel="stylesheet" href="{{ asset('vendor/pace/pace.css') }}">
<script src="{{ asset('vendor/pace/pace.min.js') }}"></script>
<!--vendors-->
<link rel="stylesheet" type="text/css" href="{{ asset('/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('/vendor/jquery-scrollbar/jquery.scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/timepicker/bootstrap-timepicker.min.css') }}">
<link href="https://fonts.googleapis.com/css?family=Hind+Vadodara:400,500,600" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('fonts/jost/jost.css') }}">
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

<!--Material Icons-->
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/materialdesignicons/materialdesignicons.min.css') }}">
<!--Bootstrap + atmos Admin CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('css/atmos.min.css') }}">
<!-- Additional library for page -->

</head>
<body class="jumbo-page">

<main class="admin-main  bg-pattern">
    <div class="container">
        <div class="row m-h-100 ">
            <div class="col-md-8 col-lg-4  m-auto">
                <div class="card shadow-lg ">
                    <div class="card-body ">
                        <div class=" padding-box-2 ">
                            <div class="text-center p-b-20 pull-up-sm">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-title rounded-circle bg-pink"> <i class="mdi mdi-key-change"></i> </span>
                                </div>
                            </div>
                            <h3 class="text-center">Recuperar contraseña</h3>
                            <form id="form-data" action="{{url('api/v1/recuperar-cuenta/reset')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                                <div class="form-group">
                                    <label>Correo</label>
                                    <div class="input-group input-group-flush mb-3">
                                        <input type="email" class="form-control not-empty form-control-prepended" name="email" placeholder="tucorreo@dominio.com" data-msg="Correo">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <span class=" mdi mdi-email "></span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="small">Te enviaremos un correo electrónico con una nueva contraseña.</p>
                                </div>
                                <div class="form-group">
                                    <button class="btn text-uppercase btn-block save btn-primary">Recuperar contraseña</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/popper/popper.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('vendor/listjs/listjs.min.js') }}"></script>
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/systemFunctions.js')}}"></script>
<script src="{{ asset('js/general-ajax.js')}}"></script>
<script src="{{ asset('js/validfunctions.js')}}"></script>
<script src="{{ asset('vendor/DataTables/datatables.min.js')}}"></script>
<script src="{{ asset('js/globalFunctions.js')}}"></script>
<script src="{{ asset('js/atmos.min.js') }}"></script>
<script src="{{ asset('vendor/blockui/jquery.blockUI.js')}}"></script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
<!--page specific scripts for demo-->

</body>
</html>
