<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpeiData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'spei_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['speiable_id', 'speiable_type', 'type', 'agreement', 'bank', 'clabe', 'name', 'due_date', 'due_date_string'];

    /**
     * Obtiene la orden o el pedido asociado al registro
     *
     */
    public function speiable()
    {
        return $this->morphTo();
    }
}
