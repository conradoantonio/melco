<?php

namespace App\Http\Controllers;

use \App\Banner;
use \App\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannersController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Banners";
        $menu = "Configuración";
        $items = Banner::orderBy('id', 'desc')->get();

        if ($req->ajax()) {
            return view('banners.table', compact(['items']));
        }
        return view('banners.index', compact(['items', 'menu' , 'title']));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Banners";
        $item = null;

        if ($id) {
            $item = Banner::find($id);
        }
        return view('banners.form', compact(['item', 'menu', 'title']));
    }

    /**
     * Save a new resource.
     *
     */
    public function save(Request $req)
    {
        $item = New Banner;

        /*if ($req->tipo == 1) { $resize = ['width' => 828, 'height' => 202]; }
        else { $resize = ['width' => 828, 'height' => 333]; }*/
        $img = $this->upload_file( $req->file('avatar'), 'img/espacios-app', true/*, $resize*/ );

        $item->img = $img;
        $item->tipo = $req->tipo;

        $item->save();

        return response(['url' => url('configuracion/banners'), 'status' => 'success', 'msg' => 'Éxito guardando el banner'], 200);
    }

    /**
     * Update a resource.
     *
     */
    public function update(Request $req)
    {
        $item = Banner::find($req->id);

        /*if ($req->tipo == 1) { $resize = ['width' => 828, 'height' => 202]; }
        else { $resize = ['width' => 828, 'height' => 333]; }*/

        if (! $item ) { return response(['msg' => 'Banner no encontrado, trate nuevamente recargando esta página'], 404); }

        $img = $this->upload_file( $req->file('avatar'), 'img/espacios-app', true/*, $resize*/ );

        $img ? $item->img = $img : '';
        $item->tipo = $req->tipo;

        $item->save();

        return response(['url' => url('configuracion/banners'), 'status' => 'success', 'msg' => 'Éxito actualizando el banner'], 200);
    }

    /**
     * Delete the specified resource.
     *
     */
    public function delete(Request $req)
    {
    	$banners = Banner::whereIn('id', $req->ids)->get();
    	if ($banners) {
            foreach ($banners as $banner) {
                File::delete(public_path($banner->img));
                $banner->delete();
            }
    			
            return response(['url' => url('configuracion/banners'), 'status' => 'success', 'msg' => 'Éxito eliminando el banner'], 200);
    	} else {
            return response(['msg' => 'Banner no encontrado', 'status' => 'error'], 404);
    	}
	}

    /**
     * Change the status of the specified resource.
     *
     */
    public function changeStatus(Request $req)
    {
        $users = Banner::whereIn('id', $req->ids)
        ->update(['status' => $req->change_to]);

        if ( $users ) {
            return response(['url' => url('configuracion/banners'), 'status' => 'success', 'msg' => 'Éxito cambiando el status del registro'], 200);
        } else {
            return response(['msg' => 'Registro no encontrado o inválido', 'status' => 'error'], 404);
        }
    }
}
