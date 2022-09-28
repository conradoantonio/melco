<?php

namespace App\Http\Controllers;

use Excel;

use \App\User;
use \App\Sucursal;

use Illuminate\Http\Request;

class ClientesController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Clientes";
        $menu = "Usuarios";

        $items = User::whereHas('role', function($query) {
            $query->where('descripcion', 'Cliente');
        })
        ->get();

        if ( $req->ajax() ) {
            return view('users.customers.table', compact('items'));
        }
        return view('users.customers.index', compact('items', 'menu', 'title'));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $items = User::filter_rows( auth()->user(), [3], $req->status );

        return view('users.customers.table', compact(['items']));
    }
	
	/**
     * Show the form for creating/editing a user franchise.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de cliente";
        $menu = "Usuarios";
        $item = null;

        if ( $id ) {
            $item = User::where('id', $id)->where('role_id', 3)->first();
        }
        // return view('users.customers.form', compact(['item', 'sucursales', 'menu', 'title']));
        return view('users.customers.form', compact(['item', 'menu', 'title']));
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = User::find($req->id);

        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }

        $photo = $this->upload_file($req->file('photo'), 'img/users/clientes', true);

        $item->fullname = $req->fullname;
        $item->email = $req->email;
        $req->password ? $item->password = bcrypt($req->password) : '';
        $item->phone = $req->phone;
        $photo ? $item->photo = $photo : '';

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('usuarios/clientes'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = User::whereIn('id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('usuarios/clientes'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('usuarios/clientes')], 404);
        }
    }

    /**
     * Change the status of the specified resource.
     *
     */
    public function changeStatus(Request $req)
    {
        $users = User::whereIn('id', $req->ids)
        ->update(['status' => $req->change_to]);

        if ( $users ) {
            return response(['url' => url('usuarios/clientes'), 'status' => 'success', 'msg' => 'Éxito cambiando el status del usuario'], 200);
        } else {
            return response(['msg' => 'Usuario no encontrado o inválido', 'status' => 'error'], 404);
        }
    }

    /**
     * Use Excel instance to export all items at once.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $req)
    {
    	$rows = array();
        $items = User::filter_rows(auth()->user(), [3], $req->status, $req->sucursal_id);

        foreach ( $items as $item ) {
            $photo = explode('/', $item->photo);
            $path = null;
            
            if ( $photo ) {
                $path = @$photo[2];
            }

            $rows [] = 
                [
                    'Nombre completo' => $item->fullname,
                    'Total de pedidos' => (count($item->pedidos) ? $item->pedidos->count() : 0),
                    'Gasto total' => $item->pedidos->sum('total') ? '$'.$item->pedidos->sum('total') : '$0',
                    'Correo' => $item->email,
                    'Teléfono' => $item->phone,
                    'ID para notificaciones' => $item->player_id ? $item->player_id : 'No asignado',
                    'Status' => $item->status == 1 ? 'Activo' : 'Deshabilitado',
                    'foto' => $path,
                ];
        }

        Excel::create('Lista de clientes', function( $excel ) use ( $rows ) {
            $excel->sheet('Hoja 1', function( $sheet ) use ( $rows ) {
                $sheet->cells('A:H', function( $cells ) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:H1', function( $cells ) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray( $rows );
            });
        })->export('xlsx');
    }
}
