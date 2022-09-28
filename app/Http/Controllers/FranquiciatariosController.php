<?php

namespace App\Http\Controllers;

use Excel;

use \App\User;
use \App\Sucursal;

use Illuminate\Http\Request;

class FranquiciatariosController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
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
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $items = User::filter_rows( auth()->user(), [2], $req->status, $req->sucursal_id );

        return view('users.franchises.table', compact(['items']));
    }
	
	/**
     * Show the form for creating/editing a user franchise.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de franquiciatario";
        $menu = "Usuarios";
        $item = null;
        $sucursales = Sucursal::all();

        if ( $id ) {
            $item = User::where('id', $id)->where('id_role', 2)->first();
        }
        return view('users.franchises.form', compact(['item', 'sucursales', 'menu', 'title']));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $sucursal = Sucursal::find($req->id_sucursales);

        if (! $sucursal ) { return response(['msg' => 'ID de sucursal no encontrado', 'status' => 'error'], 404); }

        $photo = $this->upload_file($req->file('avatar'), 'img/users', true);

        $item = New User;

        $item->id_role = 2;
        $item->fullname = $req->fullname;
        $item->email = $req->email;
        $item->password = bcrypt($req->password);
        $item->phone = $req->phone;
        $item->id_sucursales = $sucursal->id;
        $item->photo = $photo ? $photo : 'img/users/default.jpg';

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('usuarios/franquiciatarios'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = User::find($req->id);
    	$sucursal = Sucursal::find($req->id_sucursales);

        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }
        if (! $sucursal ) { return response(['msg' => 'ID de sucursal no encontrado', 'status' => 'error'], 404); }

        $photo = $this->upload_file($req->file('avatar'), 'img/users', true);

        $item->fullname = $req->fullname;
        $item->email = $req->email;
        $req->password ? $item->password = bcrypt($req->password) : '';
        $item->phone = $req->phone;
        $item->id_sucursales = $sucursal->id;
        $photo ? $item->photo = $photo : '';

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('usuarios/franquiciatarios'), 'status' => 'success'], 200);
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
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('usuarios/franquiciatarios'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('usuarios/franquiciatarios')], 404);
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
        //delete();
        if ( $users ) {
            return response(['url' => url('usuarios/franquiciatarios'), 'status' => 'success', 'msg' => 'Éxito cambiando el status del usuario'], 200);
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
        $items = User::filter_rows(auth()->user(), [2], $req->status, $req->sucursal_id);

        foreach ( $items as $item ) {
            $photo = explode('/', $item->photo);
            $path = null;
            
            if ( $photo ) {
                $path = @$photo[2];
            }

            $rows [] = 
                [
                    'Nombre completo' => $item->fullname,
                    /*Modificar esto para que agarre el total de sus sucursales*/
                    'Total de pedidos recibidos' => ($item->pedidos->count() ? : 0),
                    'Ingreso por sucursal' => ($item->pedidos->sum('total') ? '$'.$item->pedidos->sum('total') : '$0'),
                    'Correo' => $item->email,
                    'Teléfono' => $item->phone,
                    'Status' => $item->status == 1 ? 'Activo' : 'Deshabilitado',
                    'foto' => $path,
                ];
        }

        Excel::create('Lista de franquiciatarios', function( $excel ) use ( $rows ) {
            $excel->sheet('Hoja 1', function( $sheet ) use ( $rows ) {
                $sheet->cells('A:G', function( $cells ) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:G1', function( $cells ) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray( $rows );
            });
        })->export('xlsx');
    }
}
