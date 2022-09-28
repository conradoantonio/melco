<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'impuesto';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'imp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['imp_id', 'nombre', 'impuesto', 'impreso', 'tras', 'local', 'aplicarIVA', 'orden', 'status', 'tipoFactor'];

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
    public function articulos()
    {
        return $this->belongsToMany('App\Articulo', 'articuloimpuesto', 'imp_id', 'art_id');
    }
}

