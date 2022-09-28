<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('') }}">
    <meta name="user-id" content="{{ auth()->user()->id }}">
    <title>@yield('title', isset($title) ? $title .' | '.env('APP_NAME') : env('APP_NAME'))</title>
    <script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}"/>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('vendor/pace/pace.css') }}">
    <script src="{{ asset('vendor/pace/pace.min.j') }}s"></script>
    <!--vendors-->
    <link rel="stylesheet" type="text/css" href="https://rawgit.com/noppa/text-security/master/dist/text-security.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/jquery-scrollbar/jquery.scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/timepicker/bootstrap-timepicker.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Hind+Vadodara:400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fonts/jost/jost.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">

    <!--Material Icons-->
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/materialdesignicons/materialdesignicons.min.css') }}">
    <link href="{{ asset('css/main.css') }}" media="all" rel="stylesheet" type="text/css" />

    <!--Bootstrap + atmos Admin CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/atmos.css') }}">
    <!-- Additional library for page -->
    <link rel="stylesheet" href="{{ asset('vendor/DataTables/datatables.min.css')}}">
    <link rel="stylesheet" href="{{ asset('vendor/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

    {{-- Summernote --}}
    <link rel="stylesheet" href="{{ asset('vendor/summernote/summernote-bs4.css') }}"/>


</head>
<body class="sidebar-pinned">
<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <!-- begin sidebar branding-->
        {{-- <img class="admin-brand-logo" src="{{ asset('img/logo.png') }}" width="40" alt="atmos Logo"> --}}
        <span class="text-center admin-brand-content font-secondary"><a href="{{url('dashboard')}}"> Melwin</a></span>
        <!-- end sidebar branding-->
        <div class="ml-auto">
            <!-- sidebar pin-->
            <a href="#" class="admin-pin-sidebar btn-ghost btn btn-rounded-circle"></a>
            <!-- sidebar close for mobile device-->
            <a href="#" class="admin-close-sidebar"></a>
        </div>
    </div>
    <div class="admin-sidebar-wrapper js-scrollbar">
        <ul class="menu">
            <li class="menu-item {{ in_array($menu, ['Inicio']) ? 'active opened' : ''}}">
                <a href="{{url('dashboard')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Inicio</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-view-dashboard-outline"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ in_array($menu, ['Mi perfil']) ? 'active opened' : ''}}">
                <a href="{{url('mi-perfil')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Mi perfil</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-account"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ $menu == 'Productos' ? 'active' : ''}}">
                <a href="{{url('products')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Productos (Frutas y verduras)</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-tag-multiple"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ $menu == 'Artículos' ? 'active' : ''}}">
                <a href="{{url('articulos')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Artículos (Tecnología, muebles, etc)</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-checkbox-intermediate"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ in_array($menu, ['Categorías']) ? 'active opened' : ''}}">
                <a href="{{url('categorias')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Categorías</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-format-list-bulleted"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ in_array($menu, ['Pedidos']) ? 'active opened' : ''}}">
                <a href="{{url('pedidos')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Pedidos (Frutas y verduras)</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-cart"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ in_array($menu, ['Órdenes']) ? 'active opened' : ''}}">
                <a href="{{url('ordenes')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Órdenes (Tecnología, muebles, etc)</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-truck-delivery"></i>
                    </span>
                </a>
            </li>

             <li class="menu-item {{ in_array($menu, ['Guías Skydropx']) ? 'active opened' : ''}}">
                <a href="{{url('skydropx-guides')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Guías Skydropx</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-file"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ $menu == 'Notificaciones push' ? 'active' : ''}}">
                <a href="{{url('notificaciones-push')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Notificaciones push</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-bell"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ $menu == 'Formulario de contacto' ? 'active' : ''}}">
                <a href="{{url('formulario-de-contacto')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Formulario de contacto</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-account-question"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item {{ in_array($menu, ['Usuarios']) ? 'active opened' : ''}}">
                <a href="#" class="open-dropdown menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Usuarios<span class="menu-arrow"></span></span>
                    </span>
                    <span class="menu-icon"><i class="icon-placeholder mdi mdi-account-group"></i></span>
                </a>
                <!--submenu-->
                <ul class="sub-menu" style="{{ $menu == 'Usuarios' ? 'display: block' : 'display: none'}};">
                    <li class="menu-item">
                        {{-- <a href="{{url('usuarios/proveedores')}}" class="menu-link">
                            <span class="menu-label"><span class="menu-name {{ ( in_array($menu, ['Usuarios']) && in_array($title, ['Usuarios proveedores', 'Formulario de proveedor']) ) ? 'sub-ative' :'' }}">Proveedores</span></span>
                        </a> --}}
                        <a href="{{url('usuarios/clientes')}}" class="menu-link">
                            <span class="menu-label"><span class="menu-name {{ ( in_array($menu, ['Usuarios']) && in_array($title, ['Clientes', 'Formulario de cliente']) ) ? 'sub-ative' :'' }}">Clientes</span></span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ in_array($menu, ['Configuración']) ? 'active opened' : ''}}">
                <a href="#" class="open-dropdown menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Configuración<span class="menu-arrow"></span></span>
                    </span>
                    <span class="menu-icon"><i class="icon-placeholder mdi mdi-cogs"></i></span>
                </a>
                <!--submenu-->
                <ul class="sub-menu" style="{{ $menu == 'Configuración' ? 'display: block' : 'display: none'}};">
                    <li class="menu-item">
                        <a href="{{url('configuracion/politicas-de-privacidad')}}" class="menu-link">
                            <span class="menu-label"><span class="menu-name {{ ( in_array($menu, ['Configuración']) && in_array($title, ['Políticas de privacidad']) ) ? 'sub-ative' :'' }}">Políticas de privacidad</span></span>
                        </a>
                        <a href="{{url('configuracion/banners')}}" class="menu-link">
                            <span class="menu-label"><span class="menu-name {{ ( in_array($menu, ['Configuración']) && in_array($title, ['Banners']) ) ? 'sub-ative' :'' }}">Banners</span></span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ in_array($menu, ['Preguntas frecuentes']) ? 'active opened' : ''}}">
                <a href="{{url('preguntas-frecuentes')}}" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Preguntas frecuentes</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-comment-question"></i>
                    </span>
                </a>
            </li>

            <li class="menu-item log-out">
                <a href="javascript:;" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name">Cerrar sesión</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder mdi mdi-logout"></i>
                    </span>
                </a>
            </li>
        </ul>{{-- Ul menu container --}}
    </div>

</aside>
<main class="admin-main">
    <!--site header begins-->
    <header class="admin-header">
        <a href="#" class="sidebar-toggle" data-toggleclass="sidebar-open" data-target="body"> </a>
        <nav class=" mr-auto my-auto"></nav>
        <nav class=" ml-auto">
            <ul class="nav align-items-center">
                <li class="nav-item d-none">
                    <div class="dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-24px mdi-bell-outline"></i>
                            <span class="notification-counter"></span>
                        </a>
                        <div class="dropdown-menu notification-container dropdown-menu-right">
                            <div class="d-flex p-all-15 bg-white justify-content-between border-bottom ">
                                <a href="#!" class="mdi mdi-18px mdi-settings text-muted"></a>
                                <span class="h5 m-0">Notifications</span>
                                <a href="#!" class="mdi mdi-18px mdi-notification-clear-all text-muted"></a>
                            </div>
                            <div class="notification-events bg-gray-300">
                                <div class="text-overline m-b-5">today</div>
                                <a href="#" class="d-block m-b-10">
                                    <div class="card">
                                        <div class="card-body"> <i class="mdi mdi-circle text-success"></i> All systems operational.</div>
                                    </div>
                                </a>
                                <a href="#" class="d-block m-b-10">
                                    <div class="card">
                                        <div class="card-body"> <i class="mdi mdi-upload-multiple "></i> File upload successful.</div>
                                    </div>
                                </a>
                                <a href="#" class="d-block m-b-10">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="mdi mdi-cancel text-danger"></i> Your holiday has been denied
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar avatar-sm avatar-online">
                            <img src="{{ asset(auth()->user()->photo)}}" alt="..." class="avatar-img rounded-circle">
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{url('mi-perfil')}}">Cambiar contraseña</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item log-out" href="javascript:;">Cerrar sesión</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <!--site header ends -->
    @yield('content')
</main>

<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{ asset('vendor/popper/popper.js')}}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{ asset('vendor/select2/js/select2.full.min.js')}}"></script>
<script src="{{ asset('vendor/jquery-scrollbar/jquery.scrollbar.min.js')}}"></script>
<script src="{{ asset('vendor/listjs/listjs.min.js')}}"></script>
<script src="{{ asset('vendor/moment/moment.min.js')}}"></script>
<script src="{{ asset('vendor/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ asset('vendor/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{ asset('js/atmos.min.js')}}"></script>
<script src="{{ asset('vendor/DataTables/datatables.min.js')}}"></script>
<script src="{{ asset('js/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/systemFunctions.js')}}"></script>
<script src="{{ asset('js/general-ajax.js')}}"></script>
<script src="{{ asset('js/validfunctions.js')}}"></script>
<script src="{{ asset('js/globalFunctions.js')}}"></script>
<script src="{{ asset('vendor/blockui/jquery.blockUI.js')}}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ asset('vendor/timepicker/bootstrap-timepicker.min.js')}}"></script>
<script src="{{ asset('vendor/dropzone/dropzone.js') }}"></script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>


{{-- Summernote --}}
<script src="{{ asset('/vendor/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('/js/summernote-data.js') }}"></script>

{{-- Printable --}}
<script src="{{ asset('js/invoice-print.js') }}"></script>

<!--page specific scripts for demo-->

<!--Additional Page includes-->
<script src="{{ asset('vendor/apexchart/apexcharts.min.js')}}"></script>
<!--chart data for current dashboard-->
<script src="{{ asset('/js/dashboard-02.js')}}"></script>

<script type="text/javascript">
    id_photos = [];
    var baseUrl = "{{url('')}}";
    var current_user_id = $('meta[name=user-id]').attr('content');
</script>
</body>
</html>
