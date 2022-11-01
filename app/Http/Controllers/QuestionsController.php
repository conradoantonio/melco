<?php

namespace App\Http\Controllers;

use \App\Question;

use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Formulario de contacto";
        $items = Question::orderBy('id', 'desc')->get();

        if ($req->ajax()) {
            return view('questions.table', compact('items'));
        }
        return view('questions.index', compact('items', 'menu', 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Formulario de contacto";
        $item = null;
        if ($id) {
            $item = Question::find($id);
        } else {
            $item = new Question();
        }
        
        return view('questions.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = new Question();

        $item->nombre = $req->nombre;

        $item->save();

        return response(['msg' => 'Registro guardado exitósamente correctamente', 'url' => url('registros'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Question::find($req->id);
        if (!$item) {
            return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('registros')], 404);
        }

        $item->respuesta = $req->respuesta;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('formulario-de-contacto'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = Question::whereIn('id', $req->ids)
            ->delete();

        if ($items) {
            return response(['msg' => 'Éxito eliminando ' . $msg, 'url' => url('registros'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de ' . $msg, 'status' => 'error', 'url' => url('registros')], 404);
        }
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $req)
    {
        $item = Question::find($req->id);
        if (!$item) {
            return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('registros')], 404);
        }

        $item->activo = $req->activo;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('registros'), 'status' => 'success', 'item' => $item], 200);
    }
}
