<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \App\User;
use \App\Direccion;


class AddressesController extends Controller
{
    /**
     * Obtiene las direcciones vinculadas a un usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function getAddresses(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $data = Direccion::where('user_id', $user->id)->where('status', 1)->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Tarjetas enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay tarjetas por mostrar', 'data' => $data, 'status' => 'error'], 200);
    }

    /**
     * Save an address for an user
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {   
        $user = User::find($req->user_id);

        // dd($user);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $address = New Direccion;

        $address->user_id = $user->id;
        $address->pais = 'MX';
        $address->estado = $req->estado;
        $address->ciudad = $req->ciudad;
        $address->codigo_postal = $req->codigo_postal;
        $address->colonia = $req->colonia;
        $address->calle = $req->calle;
        $address->referencias = $req->referencias;
        $address->latitud = $req->latitud;
        $address->longitud = $req->longitud;

        $address->save();

        return response(['msg' => 'Dirección guardada exitósamente', 'status' => 'success', 'data' => $address], 200);
    }

    /**
     * Delete an address for an user
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $address = Direccion::where('id', $req->direccion_id)->where('status', 1)->where('user_id', $user->id)->first();

        if (! $address ) { return response(['msg' => 'ID de dirección inválida', 'status' => 'error'], 200); }

        $address->status = 0;

        $address->save();

        return response(['msg' => 'Dirección eliminada exitósamente', 'status' => 'success'], 200);
    }
}
