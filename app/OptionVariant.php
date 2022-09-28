<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OptionVariant extends Model
{
    protected $appends = ['objectId'];

    public function getObjectIdAttribute()
    {
        return $this->id;
    }
    public function optionsValue()
    {
        $this->hasMany(optionValue::class);
    }

    public function productVariant()
    {
        $this->belongsTo(ProductVariant::class);
    }
}
