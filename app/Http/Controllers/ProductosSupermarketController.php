<?php

namespace App\Http\Controllers;

Use File;
Use Excel;

use \App\Porcion;
use \App\Sucursal;
use \App\Categoria;
use \App\ImagenSuper;
use \App\ProductoSuper;

use Illuminate\Http\Request;

class ProductosSupermarketController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Productos super market";
        $menu = "Productos";
        $items = [];/*ProductoSuper::filter_rows(auth()->user());*/
        $sucursales = Sucursal::all();
        $categorias = Categoria::all();

        if ( $req->ajax() ) {
            return view('productos_supermarket.table', compact('items'));
        }
        return view('productos_supermarket.index', compact('items', 'sucursales', 'categorias', 'menu' , 'title'));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $items = ProductoSuper::filter_rows(auth()->user(), $req->id_sucursal, $req->id_categorias);

        return view('productos_supermarket.table', compact(['items']));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function getGalery($id)
    {
        $item = ProductoSuper::find($id);

        return view('productos_supermarket.galery', compact(['item']));
    }

    /**
     * Show the info of an item.
     *
     */
    public function showInfo(Request $req)
    {
        $item = ProductoSuper::with(['sucursales', 'imagenes'])->find($req->id);
        
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
        $title = "Formulario super market";
        $menu = "Productos";
        $item = null;
        $porciones = Porcion::all();
        $categorias = Categoria::all();
        $sucursales = Sucursal::all();

        if ( $id ) {
            $item = ProductoSuper::find($id);
        }
        return view('productos_supermarket.form', compact('item', 'porciones', 'categorias', 'sucursales', 'menu', 'title'));
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = ProductoSuper::find($req->id);
        $categoria = Categoria::find($req->id_categorias);
        
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }
        if (! $categoria ) { return response(['msg' => 'Seleccione un tipo de categoría válido', 'status' => 'error'], 404); }

        $img = $this->upload_file($req->file('avatar'), 'img/productos/supermarket', true);

        $item->id_categorias = $categoria->id;
        $item->clave = $req->clave;
        $item->descripcion = $req->descripcion;
        $item->precio1 = $req->precio1;
        $img ? $item->imagen_principal = $img : '';

        $item->save();

        $previous_franchises_id = $item->sucursales()->pluck('id_sucursales');
        #Check if is some product to remove
        $diff_sucursales = array_diff( $previous_franchises_id->toArray(), $req->sucursales_id);
        $item->sucursales()->sync($req->sucursales_id, false); // 2nd param = detach, we are going to detach manually if is necessary
        #Lets check if we need to remove some product
        if ( count( $diff_sucursales ) ) {
            $item->sucursales()->detach( $diff_sucursales );
        }

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('productos/supermarket'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = ProductoSuper::whereIn('art_id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('productos/supermarket'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('productos/supermarket')], 404);
        }
    }

    /**
     * Upload files (images) to the server.
     *
     * @return ['uploaded'=>true]
     */
    public function uploadContent(Request $req) 
    {
        sleep(1);//Need it for not overwrite some contents
        $resize = $req->resize ? (array) json_decode($req->resize) : false;

        $file = $this->upload_file( $req->file('file'), $req->path, $req->rename, $resize );

        if (! $file ) { return response(['msg' => 'not uploaded', 'status' => 'error'], 200); }
        
        $photo = New ImagenSuper;

        $photo->id_producto = $req->row_id;
        $photo->imagen = $file;
        $photo->peso = $req->file('file')->getClientSize();

        $photo->save();

        return response(['msg' => 'uploaded', 'status' => 'ok'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteContent(Request $req)
    {
        $items = ImagenSuper::whereIn('id', $req->ids)
        ->get();

        foreach ($items as $item) {
            File::delete(asset($item->imagen));
            $item->delete();
        }

        if ( count($items) ) {
            return response(['msg' => 'Éxito eliminando la(s) imagen(es)', 'url' => url('productos/supermarket/get-galery/'.$req->id), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Registro de imágenes no encontrados', 'status' => 'error', 'url' => url('productos/supermarket/get-galery/'.$req->id)], 404);
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

    	$products = ProductoSuper::filter_rows(auth()->user());
        
        foreach ( $products as $product ) {
            $photo = explode('/', $product->imagen_principal);
            $path = $aux = null;
            
            if ( $photo ) {
                $path = @$photo[2];
            }

            foreach( $product->sucursales as $sucursal ) {
                $aux .= $sucursal->nombre_sucursal.' ('.$sucursal->pivot->cantidad.') ';
            }
            
            #dd(implode($product->sucursales->pluck('nombre_sucursal')->toArray(), ', '));
            $rows [] = 
                [
                    'ID artículo' => $product->art_id,
                    'Clave' => $product->clave,
                    'Descripción' => $product->descripcion, 
                    'Precio' => $product->precio1,
                    'Franquicias' => $aux,
                    'Foto' => $path,
                ];
        }

        Excel::create('Productos supermarket', function( $excel ) use ( $rows ) {
            $excel->sheet('Hoja 1', function( $sheet ) use ( $rows ) {
                $sheet->cells('A:E', function( $cells ) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:E1', function( $cells ) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray( $rows );
            });
        })->export('xlsx');
    }
}