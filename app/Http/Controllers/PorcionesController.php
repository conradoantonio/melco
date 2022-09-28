<?php

namespace App\Http\Controllers;

use \App\Porcion;

use Illuminate\Http\Request;

class PorcionesController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Porciones";
        $items = Porcion::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('porciones.table', compact('items'));
        }
        return view('porciones.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Porciones";
        $item = null;
        if ( $id ) {
            $item = Porcion::find($id);
        }
        return view('porciones.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = New Porcion;

        $item->nombre_porcion = $req->nombre_porcion;
        $item->precio = $req->precio;

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('porciones'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Porcion::find($req->id);
        
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('porciones')], 404); }

        $item->nombre_porcion = $req->nombre_porcion;
        $item->precio = $req->precio;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('porciones'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = Porcion::whereIn('id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('porciones'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('porciones')], 404);
        }
    }
}
