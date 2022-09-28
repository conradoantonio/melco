<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departamento';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'dep_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dep_id', 'nombre', 'restringido', 'porcentaje', 'system', 'status'];

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
    public function categoria()
    {
        return $this->hasMany('App\CategoriaSinai', 'dep_id');
    }
}
