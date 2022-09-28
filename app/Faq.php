<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'preguntas_frecuentes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pregunta', 'respuesta'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
