<?php

namespace App\Http\Controllers\API;

use \App\User;
use \App\Tarjeta;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CardsController extends Controller
{
	/**
     * Obtiene las tarjetas vinculadas a un usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function getCards(Request $req)
    {
    	$user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $data = Tarjeta::where('user_id', $user->id)->where('status', 'activa')->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Tarjetas enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay tarjetas por mostrar', 'data' => $data, 'status' => 'error'], 200);
    }

    /**
     * Guarda una tarjeta vinculada a un usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }
        return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200);
    	$res = $this->saveOpenpayCard($req);
        if ( $res['status'] != 'success' ) { return response($res, 500); }#Card wasn't created on openpay
        return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200);
        $tarjeta = New Tarjeta;
        return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200);
        $tarjeta->user_id = $user->id;
        $tarjeta->token = $res['data']->id;
        $tarjeta->tipo = $res['data']->brand;
        $tarjeta->numero = $res['data']->card_number;
        $tarjeta->status = 'activa';

        $tarjeta->save();
        return response(['msg' => 'Tarjeta guardada exitósamente', 'data' => $tarjeta, 'status' => 'success'], 200);
    }

    /**
     * Elimina una tarjeta vinculada a un usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

    	$tarjeta = Tarjeta::where('id', $req->tarjeta_id)->where('user_id', $user->id)->first();

        if (! $tarjeta ) { return response(['msg' => 'ID de tarjeta inválido', 'status' => 'error'], 200); }#Customer wasn't created on openpay
        
    	$res = $this->deleteOpenpayCard($req, $tarjeta->token);
        if ( $res['status'] != 'success' ) { return response($res, 500); }#Card wasn't created on openpay

        $tarjeta->delete();

        return response(['msg' => 'Tarjeta eliminada exitósamente', 'status' => 'success'], 200);
    }
}
