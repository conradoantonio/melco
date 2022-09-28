<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'pregunta', 'respuesta', 'status'];

    /**
     * Get the user related to the record
     *
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
   	* Get the users filtered by the given values.
   	*/
  	static function filter_rows($l_usr, $status = null, $user_id = null, $fecha_inicio = null, $fecha_fin = null)
  	{
	    if ($l_usr->id_role == 1) { #Admin
	      	$rows = Question::query();
	    } else { #Any other role wouldn't be able to get any data
	      	return [];
	    }

	    if ( $status !== null ) {
	      	$rows = $rows->where('status', $status);
	    }

	    if ( $user_id !== null ) {
	      	$rows = $rows->where('user_id', $user_id);
	    }

	    if ( $fecha_inicio !== null ) {
	     	$rows = $rows->whereRaw('fecha >= "' . $fecha_inicio . '"');
	    	#$rows = $rows->where('fecha', '>=', $fecha_inicio);
	    }

	    if ( $fecha_fin !== null ) {
	      	$rows = $rows->whereRaw('fecha <= "' . $fecha_fin . '"');
	      	#$rows = $rows->where('fecha', '<=', $fecha_fin);
	    }

	    return $rows->get();
  	}
}
