<?php

namespace App\Http\Controllers;

use \App\Faq;

use \App\Events\PusherEvent;

use Illuminate\Http\Request;

class FaqsController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Preguntas frecuentes";
        $items = Faq::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('faqs.table', compact('items'));
        }
        return view('faqs.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Preguntas frecuentes";
        $item = null;
        if ( $id ) {
            $item = Faq::find($id);
        }
        return view('faqs.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = New Faq;

        $item->pregunta = $req->pregunta;
        $item->respuesta = $req->respuesta;

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('preguntas-frecuentes'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Faq::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('preguntas-frecuentes')], 404); }

        $item->pregunta = $req->pregunta;
        $item->respuesta = $req->respuesta;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('preguntas-frecuentes'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'las preguntas' : 'la pregunta';
        $item = Faq::whereIn('id', $req->ids)
        ->delete();
        //->update(['status' => $req->status]);

        if ( $item ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('preguntas-frecuentes'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('preguntas-frecuentes')], 404);
        }
    }
}
