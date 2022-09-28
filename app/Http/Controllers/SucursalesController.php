<?php

namespace App\Http\Controllers;

use \App\Sucursal;

use Illuminate\Http\Request;

class SucursalesController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Sucursales";
        $items = Sucursal::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('sucursales.table', compact('items'));
        }
        return view('sucursales.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de sucursal";
        $menu = "Sucursales";
        $item = null;
        if ( $id ) {
            $item = Sucursal::find($id);
        }
        return view('sucursales.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = New Sucursal;

        $item->nombre_sucursal = $req->nombre_sucursal;
        $item->direccion = $req['search-box'];
        $item->cod_postal = $req->cod_postal;
        $item->lat_long = $req->latitude.','.$req->longitude;

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('sucursales'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Sucursal::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('sucursales')], 404); }

        $item->nombre_sucursal = $req->nombre_sucursal;
        $item->direccion = $req['search-box'];
        $item->cod_postal = $req->cod_postal;
        $item->lat_long = $req->latitude.','.$req->longitude;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('sucursales'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = Sucursal::whereIn('id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('sucursales'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('sucursales')], 404);
        }
    }
}
