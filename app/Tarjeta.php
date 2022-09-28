<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tarjetas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'token', 'tipo', 'numero', 'status'];

    /**
     * Get the users related to the record
     *
     */
    public function user()
    {
        return $this->hasMany('App\User', 'user_id');
    }
}
