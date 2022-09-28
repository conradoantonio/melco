<?php

namespace App\Http\Controllers\API;

use \App\User;
use \App\Question;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionsController extends Controller
{
	/**
     * Show the questions for one user
     *
     */
    public function getQuestions(Request $req)
    {
    	$user = User::where('role_id', 3)->where('id', $req->user_id)->first();

    	if (! $user ) { return response(['msg' => 'Usuario no encontrado', 'status' => 'error'], 200); }

        $data = Question::orderBy('id', 'desc')->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Registros enlistados a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay registros por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Save a question.
     *
     */
    public function saveQuestion(Request $req)
    {
        $user = User::where('role_id', 3)->where('id', $req->user_id)->first();

    	if (! $user ) { return response(['msg' => 'Usuario no encontrado', 'status' => 'error'], 200); }

        $data = New Question;

        $data->user_id = $user->id;
        $data->pregunta = $req->pregunta;

        $data->save();

        return response(['msg' => 'Registro guardado exitósamente', 'status' => 'success', 'data' => $data], 200);
    }

    /**
     * Delete a question.
     *
     */
    public function deleteQuestion(Request $req)
    {
        $user = User::where('role_id', 3)->where('id', $req->user_id)->first();

    	if (! $user ) { return response(['msg' => 'Usuario no encontrado', 'status' => 'error'], 200); }

    	$data = Question::where('id', $req->row_id)->where('user_id', $user->id)->first();

    	if (! $user ) { return response(['msg' => 'Registro no encontrado', 'status' => 'error'], 200); }

    	$data->delete();

        return response(['msg' => 'Registro eliminado exitósamente', 'status' => 'success', 'data' => $data], 200);
    }
}
