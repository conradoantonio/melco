<?php

namespace App\Http\Controllers;

use Hash;

use \App\User;

use Illuminate\Http\Request;

class MiPerfilController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $menu = $title = "Mi perfil";

        $item = auth()->user();

        if ( $req->ajax() ) {
            return view('users.system.table', compact('users'));
        }
        return view('mi-perfil.index', compact(['item', 'menu', 'title']));
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        #User must verify his identity
        if (! $req->current_password ) { return response(['msg' => 'Debes proporcionar la contraseña actual para modificar tu perfil', 'status' => 'error'], 400); }
        
        $user = auth()->user();

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 404); }

        #Given password is not the same that the current one
        if (! Hash::check( $req->current_password, $user->password ) ) { return response(['msg' => 'Contraseña incorrecta', 'status' => 'error'], 400); }

        $img = $this->upload_file($req->file('avatar'), 'img/users', true);

        $user->fullname = $req->fullname;
        $req->new_password ? $user->password = bcrypt($req->new_password) : '';
        $img ? $user->photo = $img : '';
        $user->phone = $req->phone;

        $user->save();

        return response(['status' => 'success', 'msg' => 'Perfil actualizado correctamente'], 200);
    }
}
