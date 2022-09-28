<!DOCTYPE html>
<html>
    <head>
        <title>Acceso denegado</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BDB5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
            button{
                border: none;
                background: #3a7999;
                color: #f2f2f2;
                padding: 10px;
                font-size: 18px;
                border-radius: 5px;
                position: relative;
                box-sizing: border-box;
                transition: all 500ms ease;
            }
            button:hover {
                cursor: pointer;
                background: rgba(0,0,0,0);
                color: #3a7999;
                box-shadow: inset 0 0 0 3px #3a7999;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                @if(auth()->check())
                    <div class="title">Parece ser que no tienes permiso para acceder a esta url. </div>
                    @if(auth()->user()->role_id == 1)
                        <div class="title"> Regresa a nuestro dashboard. </div>
                        <a href="{{url('dashboard')}}"><button>Menú Principal</button></a>
                    @else
                        <div class="title"> Regresar. </div>
                        <a href="{{url('comercios')}}"><button>Ver mi perfil</button></a>{{-- Cambiar por si hay un dashboard secundario --}}
                    @endif
                @else
                    <div class="title">Parece que lo que buscas no existe. <br> Inicia sesión para navegar en nuestro panel.</div>
                    <a href="{{url('')}}"><button>Login</button></a>
                @endif
            </div>
        </div>
    </body>
</html>
