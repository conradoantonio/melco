<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
  protected $fillable = ['type', 'value', 'product_id', 'price_sale', 'sku', 'stock'];

  public $timestamps = true;

  protected $appends = ['objectId', 'name'];

  public function getObjectIdAttribute()
  {
    return $this->id;
  }

  public function getNameAttribute()
  {
    return $this->sku;
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function optionsValue()
  {
    return $this->belongsToMany(OptionValue::class, 'option_variants', 'product_variant_id', 'option_value_id');
  }

  public function options()
  {
    return $this->hasManyThrough(OptionValue::class, OptionVariant::class);
  }

  public function quality()
  {
    return substr($this->sku, 0, strpos($this->sku, '-'));
  }
}
