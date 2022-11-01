<?php

namespace App\Http\Controllers;

use \App\Info;
use \App\Distancia;
use \App\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Políticas de privacidad";
        $menu = "Configuración";
        $item = Info::find(1);

        return view('configurations.terms_conditions', compact('item', 'menu', 'title'));
    }

    /**
     * Show the main view.
     *
     */
    public function showSystemConfig(Request $req)
    {
        $title = "Configuración de sistema";
        $menu = "Configuración";
        $item = Configuration::first();

        if ( $item ) {
            $time = $item->ultima_actualizacion;
            $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time)). ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.';
        }

        return view('configurations.system', compact('item', 'distancia', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     */
    public function save_terms_conditions(Request $req)
    {
        $item = $req->id ? Info::find($req->id) : New Info;
        
        $item->contenido = $req->content;
        $item->tipo = 'terminos';
        
        $item->save();

        return response(['status' => 'success', 'msg' => 'Éxito guardando las políticas de privacidad'], 200);
    }
}
