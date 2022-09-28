<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
  protected $fillable = ['name', 'option_type_id', 'presentation', 'position'];

  protected $appends = ['objectId'];

  public function getObjectIdAttribute()
  {
    return $this->id;
  }

  public function optionVariants(){
    return $this->belongsToMany(OptionVariant::class);
  }

  public function productVariants(){
    return $this->belongsToMany(ProductVariant::class, 'option_variants', 'option_value_id', 'product_variant_id' );
  }

}
