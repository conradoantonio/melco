<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport"/>
        <meta name="description" content=""/>
        <meta name="author" content=""/>

        <title>Login</title>

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <title>Login | {{ env('APP_NAME') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}"/>
        <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" sizes="16x16">
        <link rel="stylesheet" href="{{ asset('vendor/pace/pace.css') }}">

        <script src="{{ asset('vendor/pace/pace.min.js')}}"></script>
        <!--vendors-->
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" type="text/css" >
        <link rel="stylesheet" href="{{ asset('vendor/jquery-scrollbar/jquery.scrollbar.css') }}" type="text/css" >
        <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/timepicker/bootstrap-timepicker.min.css') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind+Vadodara:400,500,600">
        <link rel="stylesheet" href="{{ asset('fonts/jost/jost.css') }}">
        <!--Material Icons-->
        <link rel="stylesheet" type="text/css" href="{{ asset('fonts/materialdesignicons/materialdesignicons.min.css') }}">
        <!--Bootstrap + atmos Admin CSS-->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/atmos.min.css') }}">

        <style type="text/css">
            /*input:-webkit-autofill {
                -webkit-box-shadow: 0 0 0px 1000px white inset !important;
            }
            body{
                background: url({{asset('img/background_login.jpg')}});
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center bottom;
            }
            .opacity {
                opacity: 0.9;
            }
            input.form-control {
                background-color: transparent;
                border: 0;
                outline: 0;
                border-bottom: 2px solid #F6EA32;
            }
            button.btn{
                color: black !important;
                background: #FDEE21 !important;
            }*/
        </style>
    </head>
    <body class="jumbo-page">
        <main class="admin-main">
            <div class="container-fluid">
                <div class="row ">
                    <div class="col-lg-4  bg-white">
                        <div class="row align-items-center m-h-100">
                            <div class="mx-auto col-md-8">
                                <div class="p-b-20 text-center">
                                    <p>
                                        <img src="{{ asset('img/logo.png') }}" width="80" alt="">
                                    </p>
                                    <p class="admin-brand-content">
                                        {{env('APP_NAME')}}
                                    </p>
                                </div>
                                <h3 class="text-center p-b-20 fw-400">Login</h3>
                                <form class="needs-validation" action="{{url('login')}}" method="POST">
                                    {{csrf_field()}}
                                    <div class="form-row">
                                        <div class="form-group floating-label col-md-12">
                                            <label>Correo</label>
                                            <input type="email" required class="form-control" name="email" placeholder="Correo">
                                        </div>
                                        <div class="form-group floating-label col-md-12">
                                            <label>Contraseña</label>
                                            <input type="password" required class="form-control" name="password" placeholder="Contraseña">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block btn-lg">Ingresar</button>
                                </form>
                                <p class="text-right p-t-10">
                                    <a href="{{url('recuperar-cuenta')}}" class="text-underline">¿Olvidaste tu contraseña?</a>
                                </p>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-8 d-none d-md-block bg-cover" style="background-image: url({{ asset('img/login.jpg') }});">

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
        <script src="{{ asset('js/atmos.min.js') }}"></script>
    </body>
</html>
