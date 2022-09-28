<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaSinai extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categoria';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'cat_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cat_id', 'nombre', 'system', 'status', 'dep_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the portions related to the record
     *
     */
    public function departamento()
    {
        return $this->belongsTo('App\Departamento', 'dep_id');
    }

    /**
     * Get the portions related to the record
     *
     */
    public function articulos()
    {
        return $this->hasMany('App\Articulo', 'art_id');
    }
}
