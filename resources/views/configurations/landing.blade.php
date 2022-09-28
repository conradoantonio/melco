<html>
	<head>
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css')}}"  type="text/css" media="screen"/>
        <link rel="stylesheet" href="{{ asset('dist/css/kavach.min.css')}}"  type="text/css" media="screen"/>
        <link rel="stylesheet" href="{{ asset('dist/css/skin/default-skin.css')}}"  type="text/css" media="screen"/>
	</head>
	<body>
		{{-- <div class="text-center">
            <img src="{{asset('img/logo.png')}}" style="margin:0;" class="img-responsive" alt="" />
		</div> --}}
		<div style="text-align: justify; padding: 2% 10%;background: whitesmoke;">
			<h1 style="margin-top: 0px;">Aviso de privacidad de Dental delivery</h1>
			<p>{!! nl2br(e($item['notice_privacy'])) !!}</p>
			{{-- <p style="margin-bottom: 0px;">{!!$item['message']!!}</p> --}}
		</div>
		<div style="text-align:center; background:gray; font-size:15px; font-weight:900; padding:6px 0px; color: floralwhite">
			<span>Desarrollado por Bridge Studio</span>
		</div>
	</body>
</html>