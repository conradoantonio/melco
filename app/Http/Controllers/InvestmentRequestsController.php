<?php

namespace App\Http\Controllers;

use \App\User;
use \App\Project;
use \App\ProjectUser;
use \App\InvestmentRequest;

use \App\Events\PusherEvent;

use Illuminate\Http\Request;

class InvestmentRequestsController extends Controller
{
	/**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Solicitudes de inversión";
        $items = InvestmentRequest::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('requests.table', compact('items'));
        }
        return view('requests.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Solicitudes de inversión";
        $item = null;
        if ( $id ) {
            $item = InvestmentRequest::find($id);
            #Only edit
            if(! $item ) { return view('errors.503'); }
        }
        return view('requests.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = New InvestmentRequest;

        $item->amount = $req->amount;

        $item->save();

        return response(['msg' => 'Registro creado correctamente', 'url' => url('solicitudes-de-inversion'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = InvestmentRequest::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('solicitudes-de-inversion')], 404); }

        $item->amount = $req->amount;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('solicitudes-de-inversion'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     */
    public function change_status(Request $req)
    {
        $item = InvestmentRequest::find($req->id);

        if (! $item ) { return response(['msg' => 'Registro no encontrado', 'status' => 'error'], 404); }

    	$old = ProjectUser::where('project_id', $item->project_id)->where('user_id', $item->user_id)->first();
        $project = Project::where('id', $item->project_id)->first();

    	#Accepted
    	if ( $req->change_to == 1 ) {
    		if ( $old ) { return response(['msg' => 'El usuario ya cuenta con una inversión en este proyecto, rechace este registro porfavor', 'status' => 'error'], 404); }

    		$row = New ProjectUser;

    		$row->project_id = $item->project_id;
			$row->user_id = $item->user_id;
			$row->amount = $item->amount;

			$row->save();

            #Let's update total collected for the project
            $sum = ProjectUser::where('project_id', $project->id)->sum('amount');

            $project->total_collected = $sum;

            $project->save();
    	}
    	$item->delete();

        return response(['url' => url('solicitudes-de-inversion'), 'status' => 'success', 'msg' => 'Éxito actualizando este registro'], 200);
    }
}
