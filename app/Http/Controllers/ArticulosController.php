<?php

namespace App\Http\Controllers;

use Excel;

use \App\Articulo;
use \App\TipoArticulo;

use Illuminate\Http\Request;

class ArticulosController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Artículos";
        $menu = "Artículos";
        $items = Articulo::filter_rows(200);
        $tipos = TipoArticulo::all();

        if ( $req->ajax() ) {
            return view('articulos.table', compact('items'));
        }
        return view('articulos.index', compact('items', 'tipos', 'menu' , 'title'));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $items = Articulo::filter_rows(null, null, $req->tipo_articulo_id, $req->search, $req->order_by);

        return view('articulos.table', compact(['items']));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function getGalery($id)
    {
        $item = Articulo::find($id);

        return view('articulos.galery', compact(['item']));
    }

    /**
     * Show the info of an item.
     *
     */
    public function showInfo(Request $req)
    {
        $item = Articulo::with(['sucursales', 'imagenes'])->find($req->id);
        
        if (! $item ) { return response(['msg' => 'ID de producto inválido', 'status' => 'error'], 404); }

        return $item;
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de artículos";
        $menu = "Artículos";
        $item = null;
        $tipos = TipoArticulo::all();

        if ( $id ) {
            $item = Articulo::find($id);
        }
        return view('articulos.form', compact('item', 'tipos', 'menu', 'title'));
    }

    /**
     * Save a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $tipoArticulo = TipoArticulo::find($req->tipo_articulo_id);
        
        if (! $tipoArticulo ) { return response(['msg' => 'Seleccione un tipo de categoría válido', 'status' => 'error'], 404); }

        $item = New Articulo();

        $img = $this->upload_file($req->file('avatar'), 'img/articulos', true);

        $item->tipo_articulo_id = $tipoArticulo->id;
        $item->nombre = $req->nombre;
        $item->stock = $req->stock;
        $item->weight = $req->weight;
        $item->descripcion = $req->descripcion;
        $item->precio = number_format($req->precio, 2, '.', '');
        $item->imagen = $img ? $img : 'img/no-image.png';

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('articulos'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Articulo::find($req->id);
        $tipoArticulo = TipoArticulo::find($req->tipo_articulo_id);
        
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }
        if (! $tipoArticulo ) { return response(['msg' => 'Seleccione un tipo de categoría válido', 'status' => 'error'], 404); }

        $img = $this->upload_file($req->file('avatar'), 'img/articulos', true);

        $item->tipo_articulo_id = $tipoArticulo->id;
        $item->nombre = $req->nombre;
        $item->stock = $req->stock;
        $item->weight = $req->weight;
        $item->descripcion = $req->descripcion;
        $item->precio = number_format($req->precio, 2, '.', '');
        $img ? $item->imagen = $img : '';

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('articulos'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = Articulo::whereIn('art_id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('articulost'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('articulos')], 404);
        }
    }

    /**
     * Use Excel instance to export all products at once.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $req)
    {
    	$rows = $aux = array();

        $products = Articulo::filter_rows(null, null, $req->tipo_articulo_id, $req->search, $req->order_by);
        
        foreach ( $products as $product ) {
            $photo = explode('/', $product->imagen);
            $path = $aux = null;
            
            if ( $photo ) {
                $path = @$photo[2];
            }
            
            $rows [] = 
                [
                    'ID artículo' => $product->id,
                    'Nombre' => $product->nombre,
                    'Descripción' => $product->descripcion, 
                    'Precio' => $product->precio / 100,
                    'Stock actual' => $product->stock,
                    '¿Producto destacado?' => $product->destacado ? 'Si' : 'No',
                    'Foto' => $path,
                ];
        }

        Excel::create('Artículos', function( $excel ) use ( $rows ) {
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
