<html>
	<head></head>
	<body>
		<div>
			<!-- Cambiar el nombre del header -->
			<img src="{{asset('img/mail-header.jpg')}}" style='width: 100%;'>
		</div>
		<div style="text-align: justify; padding: 2% 10%;background: whitesmoke;">
			<h1 style="margin-top: 0px;">{{$content['title']}}</h1>
			<p style="margin-bottom: 0px;">{{$content['message']}}</p>
			<ul>
				<li><strong>Correo:</strong> {{$content['email']}}</li>
				<li><strong>Contraseña:</strong> {{$content['password']}}</li>
			</ul>
			<p><strong>Ingrese a:</strong> {{url('')}}</p>
		</div>
		<div style="text-align:center; background:#8035A0; font-size:15px; font-weight:900; padding:6px 0px; color: floralwhite">
			<span>Desarrollado por Bridge Studio</span>
		</div>
	</body>
</html>