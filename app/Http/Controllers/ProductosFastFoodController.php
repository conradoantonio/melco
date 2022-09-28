<?php

namespace App\Http\Controllers;

Use File;
Use Excel;

use \App\Porcion;
use \App\Sucursal;
use \App\Categoria;
use \App\TipoProducto;
use \App\ImagenFastFood;
use \App\ProductoFastFood;
use \App\ProductoFastFoodSucursal;

use Illuminate\Http\Request;

class ProductosFastFoodController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Productos fast food";
        $menu = "Productos";
        $items = ProductoFastFood::filter_rows(auth()->user());
        $sucursales = Sucursal::all();
        $categorias = Categoria::all();

        if ( $req->ajax() ) {
            return view('productos_fastfood.table', compact('items'));
        }
        return view('productos_fastfood.index', compact('items', 'sucursales', 'categorias', 'menu' , 'title'));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $items = ProductoFastFood::filter_rows(auth()->user(), $req->id_sucursal, $req->id_categorias);

        return view('productos_fastfood.table', compact(['items']));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function getGalery($id)
    {
        $item = ProductoFastFood::find($id);

        return view('productos_fastfood.galery', compact(['item']));
    }

    /**
     * Show the info of an item.
     *
     */
    public function showInfo(Request $req)
    {
        $item = ProductoFastFood::with(['sucursales', 'imagenes'])->find($req->id);
        
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
        $title = "Formulario fast food";
        $menu = "Productos";
        $item = null;
        $porciones = Porcion::all();
        $categorias = Categoria::all();
        $sucursales = Sucursal::all();

        if ( $id ) {
            $item = ProductoFastFood::find($id);
        }
        return view('productos_fastfood.form', compact('item', 'porciones', 'categorias', 'sucursales', 'menu', 'title'));
    }

    /**
     * Save a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $categoria = Categoria::find($req->id_categorias);

        if (! $categoria ) { return response(['msg' => 'Seleccione un tipo de categoría válido', 'status' => 'error'], 404); }

        $item = New ProductoFastFood;
        
        $img = $this->upload_file($req->file('avatar'), 'img/productos/fastfood', true);

        $item->id_categorias = $categoria->id;
        $item->clave = $req->clave;
        $item->descripcion = $req->descripcion;
        $item->precio1 = $req->precio1;
        $img ? $item->imagen_principal = $img : '';

        $item->save();

        $item->porciones()->attach($req->porciones_id);
        $item->sucursales()->attach($req->sucursales_id);
        
        return response(['msg' => 'Registro guardado exitósamente', 'url' => url('productos/fastfood'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = ProductoFastFood::find($req->id);
        $categoria = Categoria::find($req->id_categorias);
        
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }
        if (! $categoria ) { return response(['msg' => 'Seleccione un tipo de categoría válido', 'status' => 'error'], 404); }

        $img = $this->upload_file($req->file('avatar'), 'img/productos', true);

        $item->id_categorias = $categoria->id;
        $item->clave = $req->clave;
        $item->descripcion = $req->descripcion;
        $item->precio1 = $req->precio1;
        $img ? $item->imagen_principal = $img : '';

        $item->save();

        $previous_portions_id = $item->porciones()->pluck('id_porcion');
        #Check if is some product to remove
        $diff_portions = array_diff( $previous_portions_id->toArray(), $req->porciones_id);
        $item->porciones()->sync($req->porciones_id, false); // 2nd param = detach, we are going to detach manually if is necessary
        #Lets check if we need to remove some product
        if ( count( $diff_portions ) ) {
            $item->porciones()->detach( $diff_portions );
        }

        $previous_franchises_id = $item->sucursales()->pluck('id_sucursales');
        #Check if is some product to remove
        $diff_sucursales = array_diff( $previous_franchises_id->toArray(), $req->sucursales_id);
        $item->sucursales()->sync($req->sucursales_id, false); // 2nd param = detach, we are going to detach manually if is necessary
        #Lets check if we need to remove some product
        if ( count( $diff_sucursales ) ) {
            $item->sucursales()->detach( $diff_sucursales );
        }

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('productos/fastfood'), 'status' => 'success'], 200);
    }

    /**
     * Change the stock of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStock(Request $req)
    {
        $stock = (int)$req->sotck; 
        $sucursal = Sucursal::find($req->id_sucursales);
        $producto = ProductoFastFood::find($req->id);

        if (! is_int($stock) ) { return response(['msg' => 'Proporcione un valor numérico', 'status' => 'error'], 400); }
        if (! $sucursal ) { return response(['msg' => 'Sucursal inválida', 'status' => 'error'], 404); }
        if (! $producto ) { return response(['msg' => 'Producto no encontrado', 'status' => 'error'], 404); }

        $pivot = [ 'id_sucursales' => $sucursal->id, 'id_producto_fast_food' => $producto->art_id, 'cantidad' => $stock ];
        $res = ProductoFastFoodSucursal::updateOrCreate([ 'id_sucursales' => $sucursal->id, 'id_producto_fast_food' => $producto->art_id ], $pivot);

        return response(['msg' => 'Éxito modificando el stock del producto', 'url' => url('productos/fastfood'), 'status' => 'success', 'data' => $res], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = ProductoFastFood::whereIn('art_id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('productos/fastfood'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('productos/fastfood')], 404);
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
        
        $photo = New ImagenFastFood;

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
        $items = ImagenFastFood::whereIn('id', $req->ids)
        ->get();

        foreach ($items as $item) {
            File::delete(asset($item->imagen));
            $item->delete();
        }

        if ( count($items) ) {
            return response(['msg' => 'Éxito eliminando la(s) imagen(es)', 'url' => url('productos/fastfood/get-galery/'.$req->id), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Registro de imágenes no encontrados', 'status' => 'error', 'url' => url('productos/fastfood/get-galery/'.$req->id)], 404);
        }
    }

    /**
     * Use Excel instance to import many products at once.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $req)
    {
        $file = $req->excel_file;
        if ( $file ) {
            $path = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            if ( $extension == 'xlsx' || $extension == 'xls' ) {
                $data = Excel::load($path, function( $reader ) {
                    $reader->setDateFormat('Y-m-d');
                })->get();

                if (! empty( $data ) && $data->count() ) {
                    foreach ( $data as $key => $value ) {
                        #Usuario sucursal
                        if ( auth()->user()->id_role == 2 && (! auth()->user()->sucursal ) ) { return response(['msg' => 'Debes tener una franquicia asociada para cargar productos', 'status' => 'error'], 400); }

                        $insert = [
                            'clave' => $value->clave,
                            'descripcion' => $value->descripcion,
                            'precio1' => $value->precio,
                            #'imagen_principal' => $value->foto ? 'img/productos/fastfood/'.$value->foto : '',
                        ];
                        
                        $res = ProductoFastFood::updateOrCreate([
                            'clave' => $insert['clave'],
                            'descripcion' => $insert['descripcion'],
                        ], $insert);

                        #If is an franchise user, saves a pivot record between product and franchise
                        if ( auth()->user()->id_role == 2 && ( auth()->user()->sucursal ) ) {
                            $pivot = [ 'id_sucursales' => auth()->user()->sucursal->id, 'id_producto_fast_food' => $res->art_id ];
                            ProductoFastFoodSucursal::firstOrCreate($pivot, $pivot);
                        }
                    }
                }//End data count if
                $data = ['msg' => 'Registros validados correctamente', 'status' => 'success', 'url' => url('productos/fastfood')];
                return response($data, 200);
            }//End of extension if
        } else {
            return response(['msg' => 'No hay archivo para verificar', 'status' => 'error'], 404);
        }
    }

    /**
     * Use Excel instance to export all products at once.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $req)
    {
    	$rows = array();

    	$products = ProductoFastFood::filter_rows(auth()->user());
        
        foreach ( $products as $product ) {
            $photo = explode('/', $product->imagen_principal);
            $path = null;
            
            if ( $photo ) {
                $path = @$photo[3];
            }

            $rows [] = 
                [
                    'ID artículo' => $product->art_id,
                    #'Categoría' => $product->categoria ? $product->categoria->nombre_categoria : 'No especificado',
                    'Clave' => $product->clave,
                    'Descripción' => $product->descripcion, 
                    'Precio' => $product->precio1,
                    'Foto' => $path,
                ];
        }

        Excel::create('Productos fastfood', function( $excel ) use ( $rows ) {
            $excel->sheet('Hoja 1', function( $sheet ) use ( $rows ) {
                $sheet->cells('A:C', function( $cells ) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:C1', function( $cells ) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray( $rows );
            });
        })->export('xlsx');
    }
}
