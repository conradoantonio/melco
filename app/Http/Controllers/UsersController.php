<?php

namespace App\Http\Controllers;

use Hash;
use \App\User;
use Carbon\Carbon;
use \App\Role;
use \App\Sucursal;

use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function get(Request $request)
    {
      $user_id = $request->get("uid", 0);
      $user = User::find($user_id);

      //$user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                    ->toDateTimeString(),
        ]);
    }
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Usuarios de sistema";
        $menu = "Usuarios";

        $users = User::whereHas('role', function($query) {
            $query->where('env', 'sistema');
        })
        ->where('id', '!=', $this->current_user->id)
        ->get();

        $franchises = Sucursal::all();

        if ( $req->ajax() ) {
            return view('users.system.table', compact('users'));
        }
        return view('users.system.index', compact(['users', 'franchises', 'menu', 'title']));
    }

    /**
     * Show the main view.
     *
     */
    public function showFranchise(Request $req)
    {
        $title = "Usuarios franquiciatarios";
        $menu = "Usuarios";

        $items = User::whereHas('role', function($query) {
            $query->where('descripcion', 'Usuario');
        })
        ->get();

        $sucursales = Sucursal::all();
        if ( $req->ajax() ) {
            return view('users.franchises.table', compact('items'));
        }
        return view('users.franchises.index', compact('items', 'sucursales', 'menu', 'title'));
    }

    /**
     * Show the main view.
     *
     */
    public function show_customers(Request $req)
    {
        $title = "Usuarios de aplicación";
        $menu = "Usuarios";

        $users = User::whereHas('role', function($query) {
            $query->where('descripcion', 'Cliente');
        })
        ->get();

        if ($req->ajax()) {
            return view('users.customers.content', ['users' => $users]);
        }
        return view('users.customers.index', compact(['users', 'menu', 'title']));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Usuarios";
        $user = null;
        $roles = Role::where('env', 'sistema')->get();

        if ( $id ) {
            $user = User::find($id);
        }
        return view('users.system.form', compact(['user', 'roles', 'menu', 'title']));
    }

    /**
     * Show the form for creating/editing a user franchise.
     *
     * @return \Illuminate\Http\Response
     */
    public function formFranchise($id = 0)
    {
        $title = "Formulario de franquiciatario";
        $menu = "Usuarios";
        $item = null;

        if ( $id ) {
            $item = User::find($id);
        }
        return view('users.franchises.form', compact(['item', 'menu', 'title']));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        if ( count( User::user_by_email( $req->email ) ) ) {
            return response(['msg' => 'Este correo ya se encuentra en uso, trate con uno diferente.', 'status' => 'error'], 400);
        }

        $user = New User;

        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->fullname = $req->fullname;
        $user->photo = 'img/users/default.jpg';
        $user->phone = $req->phone;
        $user->role_id = $req->role_id;

        $user->save();

        $url = $req->reload_url ? : url('usuarios/sistema');

        return response(['msg' => 'Nuevo usuario registrado correctamente', 'status' => 'success', 'url' => $url], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        if ( count( User::user_by_email( $req->email, $req->old_email ) ) ) {
            return response(['msg' => 'Este correo ya se encuentra en uso, trate con uno diferente.', 'status' => 'error'], 400);
        }

        $user = User::find($req->id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 404); }

        $img = $this->upload_file($req->file('img'), 'img/users', true);


        $user->email = $req->email;
        $req->password ? $user->password = bcrypt($req->password) : '';
        $user->fullname = $req->fullname;
        $img ? $user->photo = $img : '';
        $user->phone = $req->phone;
        $req->role_id ? $user->role_id = $req->role_id : '';

        $url = $req->reload_url ? : url('usuarios/sistema');


        $user->save();

        return response(['url' => $url, 'status' => 'success', 'msg' => 'Usuario modificado exitósamente'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los usuarios selecciondos' : 'el usuario';
        $users = User::whereIn('id', $req->ids)
        ->get();
        //->delete();
        //->update(['status' => $req->status]);

        if ( $users ) {
            $url = $req->reload_url ? : url('usuarios/sistema');

            return response(['url' => $url, 'status' => 'success', 'msg' => 'Éxito eliminando '. $msg], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error'], 404);
        }
    }

    /**
     * Change the status of the specified resource.
     *
     */
    public function change_status(Request $req)
    {
        $users = User::whereIn('id', $req->ids)
        ->update(['status' => $req->change_to]);
        //delete();
        if ( $users ) {
            $url = $req->reload_url ? : url('usuarios/sistema');

            return response(['url' => $url, 'status' => 'success', 'msg' => 'Éxito cambiando el status del usuario'], 200);
        } else {
            return response(['msg' => 'Usuario no encontrado o inválido', 'status' => 'error'], 404);
        }
    }

    /**
     * Changes the user's password.
     *
     */
    public function change_password(Request $req)
    {
        $user = User::find(auth()->user()->id);

        if ( $user ) {
            if ( Hash::check( $req->current_pass, $user->clave ) ) {
                if ( $req->new_pass == $req->confirm_pass ) {
                    $user->clave = bcrypt( $req->new_pass );
                    $user->save();
                    return response(['msg' => 'Contraseña modificada exitósamente', 'status' => 'ok'], 200);
                } else {
                    return response(['msg' => 'Las contraseñas no coinciden', 'status' => 'error'], 200);
                }
            } else {
                return response(['msg' => 'Contraseña errónea', 'status' => 'error'], 200);
            }
        } else {
            return response(['msg' => 'Usuario no válido o sesión expirada', 'status' => 'error'], 403);
        }
    }

    /**
     * change the user's profile picture
     *
     */
    public function change_profile_picture(Request $req)
    {
        $user = User::find($this->current_user->id);

        if ( $user ) {
            $img = $this->upload_file($req->file('img'), 'img/profile', true);
            $user->img = $img;
            $user->save();

            return response(['msg' => 'Foto modificada exitósamente', 'status' => 'ok'], 200);
        } else {
            return response(['msg' => 'Ocurrió un error tratando de modificar la foto de perfil del usuario', 'status' => 'error'], 500);
        }
    }

    /**
     *==============================================================================================================================================
     *=                                                   Functions related with deliver profile                                                   =
     *==============================================================================================================================================
     */

    /**
     * Show the main view.
     *
     */
    public function index_deliver(Request $req)
    {
        $title = $menu = "Mi perfil";

        return view('my-profile.index', compact('menu', 'title'));
    }
}
