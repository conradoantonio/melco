<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
		<meta name="description" content="" />
		<meta name="author" content="" />

		<title>Registro | Todo a meses</title>

        <link rel="stylesheet" type="text/css" href="https://rawgit.com/noppa/text-security/master/dist/text-security.css">
		<link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css')}}"  type="text/css" media="screen"/>
        <link rel="stylesheet" href="{{ asset('dist/css/all_icons.css')}}"  type="text/css"/>
        <link rel="stylesheet" href="{{ asset('plugins/metisMenu/metisMenu.min.css')}}" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('dist/css/kavach.min.css')}}" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('dist/css/animate.css')}}" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css')}}" type="text/css"/>

        <style type="text/css">
        /* Change the white to any color ;) */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px white inset !important;
        }
        </style>
        
		<!-- Change Color CSS --> 
		<link rel="stylesheet" id="jssDefault" href="{{ asset('dist/css/skin/default-skin.css')}}" />

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
	<body>
		<!-- jQuery -->
        <script src="{{ asset('plugins/jquery/dist/jquery.min.js') }}"></script>
        
        <!-- Bootstrap Core JavaScript -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>

        <!-- Custom js-->
        <script src="{{ asset('js/validFunctions.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/generalAjax.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/sweetalert.min.js') }}"></script>
	    
	    <div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="login-panel panel panel-default">
						<div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            - Todos los campos son obligatorios <br>	
                            - El campo celular debe tener al menos 10 números y NO debe contener caracteres especiales como paréntesis, signos, etc. <br>	
                            - El correo no puede usarse en más de dos cuentas distintas <br>	
                        </div>
						<div class="panel-heading">
							<h3 class="panel-title">Crea tu cuenta</h3>
						</div>
						<div class="panel-body">
							<img src="{{asset('dist/img/logo.png')}}" class="img-responsive" alt="" />
                        	<form id="form-data" method="POST" action="{{url('register')}}" enctype="multipart/form-data" onsubmit="return false;" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="0" data-redirect="1">
								{{csrf_field()}}
								<fieldset>
									<div class="form-group hide">
                                    	<label class="form-label">Redirect to</label>
										<input class="form-control not-empty" value="profile" name="redirect" type="text" />
									</div>
									<div class="form-group">
                                    	<label class="form-label">Nombre(s)</label>
										<input class="form-control not-empty" data-msg="Nombre(s)" name="name" type="text" />
									</div>
									<div class="form-group">
                                    	<label class="form-label">Apellido(s)</label>
										<input class="form-control not-empty" data-msg="Apellido(s)" name="lastname" type="text" />
									</div>
									<div class="form-group">
                                    	<label class="form-label">Celular</label>
										<input class="form-control not-empty" data-msg="Celular" name="phone" type="text" />
									</div>
									<div class="form-group">
                                    	<label class="form-label">Correo</label>
										<input class="form-control not-empty" data-msg="Correo" name="email" type="email" />
									</div>
									<div class="form-group">
                                    	<label class="form-label">Contraseña</label>
										<input class="form-control not-empty pass-font" data-msg="Contraseña" name="password" type="text" value="" />
									</div>
                                    <button class="btn btn-login m-t-10 save" type="submit">Registrar <i class="fa fa-paper-plane"></i></button>
								</fieldset>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>

