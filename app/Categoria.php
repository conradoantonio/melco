<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $appends = ['objectId'];

    public function getObjectIdAttribute()
    {
      return $this->id;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the users related to the record
     *
     */
    public function productos()
    {
        return $this->hasMany('App\Product', 'category_id');
    }
}
