<?php

namespace App\Http\Controllers;

use DB;

use App\User;
use App\Pedido;
use App\ProductoSuper;
use App\DetallePedido;

use Illuminate\Http\Request;

use App\Events\PusherEvent;

class LoginController extends Controller
{
	/**
     * Validate the user login.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $errors = [];

        if ( auth()->attempt(['email' => $req->email, 'password' => $req->password, 'status' => 1]) ) {
            if ( auth()->user()->role->descripcion == 'Administrador' ) {
                return redirect()->to('dashboard');
            } elseif( auth()->user()->role->descripcion == 'Franquiciatario' ) {
                if ( auth()->user()->franchise ) {#Must have a related franchise
                    return redirect()->to('mi-perfil');
                } else {
                    $errors = [ 'msg' => 'Debes tener una franquicia vinculada a tu usuario, contacte al administrador'];
                    session(['email' => $req['email']]);
                    auth()->logout();
                }
            } elseif( auth()->user()->role->descripcion == 'Repartidor' ) {
                return redirect()->to('mi-perfil');
            }
        } else {
            $user = User::where('email', $req['email'])->first();
            if (! $user ) {
                session()->forget('email');
                $errors = [ 'msg' => 'Usuario inválido'];
            } else {
                if (! $user->status ) {
                    $errors = [ 'msg' => 'No tienes acceso al panel'];
                    session(['email' => $req['email']]);
                } else {
                    $errors = [ 'msg' => 'Contraseña incorrecta'];
                    session(['email' => $req['email']]);
                }
            }
            auth()->logout();
        }

        return redirect()->to('/')->withErrors($errors);
    }

	/**
     * redirect to the dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function load_dashboard()
    {
        $title = $menu = 'Inicio';

        $data = []; #$this->getDashboarData();
        $tops = []; #$this->getTopUsers();
        $productos = []; #$this->getTopProducts(1);#Supermarket
        $salesFastFood = [];#$this->getWeeklySales(1);
        $salesSupermarket = [];#$this->getWeeklySales(2);
        $salesPerHour = [];#$this->getFrequentSalesByHour();

        return view('layouts.dashboard', ['data' => ($data), 'tops' => $tops, 'topProductos' => $productos, 'salesSupermarket' => $salesSupermarket, 'salesFastFood' => ($salesFastFood), 'salesPerHour' => ($salesPerHour), 'title' => $title, 'menu' => $menu]);
    }

    /**
     * Shows the sign up form
     *
     * @return \Illuminate\Http\Response
     */
    public function sign_up()
    {
        return view('layouts.sign_up');
    }

    /**
     * Shows the sign up form
     *
     * @return \Illuminate\Http\Response
     */
    public function resetView()
    {
        return view('layouts.reset');
    }

    /**
     * Shows the sign up form
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $req)
    {
        $item = User::where('email', $req->email)->first();

        if ( $item ) {
            $newPass = str_random(8);

            $item->password = bcrypt( $newPass );
            $item->save();

            $params = array();

            $params['view'] = 'mails.reset_password';
            $params['subject'] = 'Cambio de contraseña';
            $params['user'] = $item;
            $params['email'] = $item->email;
            $params['password'] = $newPass;

            auth()->logout();

            $this->f_mail( $params );
        }

        return response(['status' => 'success', 'msg' => 'Correo enviado', 'url' => url('/')], 200);
    }

    /**
     * Get the dashboard data.
     *
     */
    public function getDashboarData()
    {
        $data = new \stdClass();

        #Total of paid orders regardless their status
        $data->totalPaidOrders = Pedido::sum('total');

        #Total of orders made by the app
        $data->totalOrders = Pedido::count();

        #Total of orders made by the app
        $data->finishedOrders = Pedido::whereHas('status', function($query){
            $query->where('descripcion', 'Entregado a cliente');
        })->count();

        #Last month orders
        $data->lastMonthOrders = Pedido::where('fecha', '>=', $this->actual_month.'-01')->count();

        #Total app users
        $data->totalAppUsers = User::whereHas('role', function($query) {
            $query->where('descripcion', 'Cliente');
        })->count();

        #Total of products registered in the system
        $data->totalProducts = ProductoSuper::count();

        #Percentage of finished services
        #$data->percentage_of_finished_services = $data->total_services == 0 ? 0 : round((($data->finished_services / $data->total_services) * 100), 0, PHP_ROUND_HALF_DOWN);

        #Percentage of banned users
        #$data->percentage_of_banned_users = $data->total_app_users == 0 ? 0 : round((($data->banned_app_users / $data->total_app_users) * 100), 0, PHP_ROUND_HALF_DOWN);

        return json_encode($data);
    }

    /**
     * Get top 10 users.
     */
    public function getTopUsers()
    {
        $pedidos = Pedido::select(DB::raw('*, SUM(total) AS "total_paid"' ))->groupBy('id_users')->orderBy('total_paid', 'desc')->limit(10)->get();

        return $pedidos;
    }

    /**
     * Get top 10 products.
     */
    public function getTopProducts($tipo)
    {
        $products = DetallePedido::select(DB::raw('*, COUNT(id_producto) AS "coincidencias"' ))
        ->groupBy('id_producto')->orderBy('coincidencias', 'desc')->whereNotNull('id_producto')
        ->whereHas('pedido', function( $query ) use ( $tipo ) {
            $query->where('id_tipo_pedido', $tipo);
        });

        if ( $tipo == 1 ) {#Supermarket
            $products = $products->with('productosSupermarket');
        } else {
            $products = $products->with('productosFastFood');
        }

        $items = $products->limit(10)->get();

        return $items;
    }

    /**
     * Get weekly sales.
     */
    public function getWeeklySales($tipo)
    {
        $day_name = array();
        $array_week_day = array();
        $array_sales_day = array();
        $current_week = array();
        $array_days = array('','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
        $data_sales = Pedido::getLastWeekSales($tipo);

        for ( $i=0; $i <= 6; $i++ ) {
            $current_date = date_create($this->actual_date);
            $current_date = date_sub($current_date, date_interval_create_from_date_string($i.' days'));
            array_push($current_week, $current_date->format('Y-m-d'));
        }

        foreach ($current_week as $day) {
            array_push($day_name, $array_days[date('N', strtotime($day))]);
        }

        foreach ($data_sales as $value) {
            array_push($array_week_day, date_create($value->fecha_ped)->format('Y-m-d'));
            array_push($array_sales_day, $value->total_paid);
        }

        $final_array = $current_week;

        foreach ($final_array as $key => $value) { $final_array[$key] = 0; }

        foreach ($array_week_day as $key => $val) {
            $found = array_search($val, $current_week);
            is_int($found) ? $final_array[$found] = $array_sales_day[$key] : '';
        }

        $object = new \stdClass();
        $object->week_days = array_reverse($day_name);
        $object->total_sales = array_reverse($final_array);

        return json_encode($object);
    }

    /**
     * Get the hours by day (for the last week).
     */
    public function getFrequentSalesByHour($tipo = null)
    {
        $day_name = $array_week_day = $current_week = array();
        $array_days = array('','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');

        for ( $i=1; $i <= 24; $i++ ) {
            $empty_hours[] = ['x' => (string)$i, 'y' => (int)0];
        }

        for ( $i=0; $i <= 6; $i++ ) {
            $current_date = date_create($this->actual_date);
            $current_date = date_sub($current_date, date_interval_create_from_date_string($i.' days'));
            array_push($current_week, $current_date->format('Y-m-d'));
        }

        foreach ($current_week as $day) {
            $formated_day = date_create($day);
            array_push( $day_name, ['name' => $array_days[ date( 'N', strtotime( $day ) ) ], 'no_dia' => (int)$formated_day->format('d'), 'fecha' => $formated_day->format('Y-m-d') ]);
        }

        #Let's push some data into day's array
        foreach ( $day_name as $key => $day ) {
            $data_sales = Pedido::getGroupedSalesByHour($tipo, $day['fecha']);
            #Reset array hours every different day
            $array_hours = $empty_hours;

            #Verify if day has records in sales
            if ( count($data_sales) ) {
                foreach ( $data_sales as $keySale => $sale ) {
                    #Search data sale per hour into the array and modify the Y value
                    $array_hours[$sale->hora-1]['y'] = (int)$sale->total_pedidos;
                }
            } else {
                for ( $i=1; $i <= 24; $i++ ) {
                    $array_hours = $empty_hours;
                }
            }
            #Add data about hours info to the current day
            $day_name[$key]['data'] = $array_hours;
        }
        return json_encode($day_name);
    }

    /**
     * Destroy's the current session.
     *
     */
    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }
}
