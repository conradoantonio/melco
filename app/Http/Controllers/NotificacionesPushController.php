<?php

namespace App\Http\Controllers;

use DB;

use \App\User;

use Illuminate\Http\Request;

class NotificacionesPushController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $menu = 'Notificaciones push';
        $start_date = $this->actual_date;
        $verifyPlayerID = null;
        $customers = User::filter_rows(auth()->user(), [3], null, null, $verifyPlayerID);

        return view('notificaciones.index', compact('menu', 'title', 'customers', 'start_date'));
    }

    /**
     * Filter the users to send a notification
     *
     * @return \Illuminate\Http\Response
     */
    public function filterUsers(Request $req)
    {
        $res = $this->getNotifcationsUSers($req);

        $customers = $res['data'];

        return response(['data' => $customers, 'msg' => 'Usuarios enlistados a continución', 'status' => 'success'], 200);
    }

    /**
    * Get the notifications parameters, so, we can decide if send an individual or a general notification. 
    * @return $this->sendNotification
    */
    public function sendPush(Request $req) 
    {
        $users_id = array();
        $type = $req->type;
        $app_id = $this->app_id;
        $app_key = $this->app_key;
        $app_icon = $this->app_icon;
        $title = $req->title;
        $content = $req->content;
        $date = $req->date;
        $time = $req->time;
        $data = array("origin" => "api_system");

        if ( $type == 1 ) {#General
            $res = $this->getNotifcationsUSers($req);

            $customers = $res['data'];

            foreach( $customers as $customer ) { $users_id[] = $customer->id; }
        } else {#Individual
            $users_id = $req->users_id;
        }

        $response = $this->sendNotification($type, $app_id, $app_key, $app_icon, $title, $content, $date, $time, $data, $users_id);

        $str_errors = '';
        if ( array_search('error', $response) ) {
            return response(['msg' => $response['msg'], "data" => $response, 'status' => 'warning'], 400);
        } else {
            return response(['msg' => $response['msg'], 'status' => 'success'], 200);
        }
    }
}
