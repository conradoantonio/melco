<?php

namespace App\Http\Controllers;

use \App\Categoria;
use \App\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class CategoriasController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Categorías";
        $items = Categoria::orderBy('id', 'desc')->get();

        if ($req->ajax()) {
            return view('categorias.table', compact('items'));
        }
        return view('categorias.index', compact('items', 'menu', 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Categorías";
        $item = null;
        if ($id) {
            $item = Categoria::find($id);
        } else {
            $item = new Categoria();
        }
        
        return view('categorias.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = new Categoria();

        $item->nombre = $req->nombre;

        $item->save();

        return response(['msg' => 'Registro guardado exitósamente correctamente', 'url' => url('categorias'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Categoria::find($req->id);
        if (!$item) {
            return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('categorias')], 404);
        }

        $item->nombre = $req->nombre;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('categorias'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = Categoria::whereIn('id', $req->ids)
            ->delete();

        if ($items) {
            return response(['msg' => 'Éxito eliminando ' . $msg, 'url' => url('categorias'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de ' . $msg, 'status' => 'error', 'url' => url('categorias')], 404);
        }
    }

    public function ajaxImage(Request $request, $id)
    {
        if ($request->isMethod('get'))
            return view('ajax_image_upload');
        else {
            $validator = Validator::make(
                $request->all(),
                [
                    'file' => 'image',
                ],
                [
                    'file.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg)'
                ]
            );
            if ($validator->fails())
                return array(
                    'fail' => true,
                    'errors' => $validator->errors()
                );
            $extension = $request->file('file')->getClientOriginalExtension();
            $dir = 'uploads/category/' . $id;
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $request->file('file')->move($dir, $filename);

            Log::info("filename => {$filename}");

            $item = Categoria::find($id);

            \File::Delete(public_path($item->image));


            $item->image = "{$dir}/{$filename}";

            $item->save();

            return response()->json(['msg' => 'success', 'filename' => "{$request->getSchemeAndHttpHost()}/{$item->image}"]);
        }
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $req)
    {
        $item = Categoria::find($req->id);
        if (!$item) {
            return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('categorias')], 404);
        }

        $item->activo = $req->activo;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('categorias'), 'status' => 'success', 'item' => $item], 200);
    }
}
