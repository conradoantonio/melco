<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Product extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'name', 'description', 'price_sale', 'size', 'quality', 'price_cost', 'location', 'stock', 'price_weight'];

    protected $appends = ['objectId'];

    public function getObjectIdAttribute()
    {
      return $this->id;
    }

    public function category()
    {
        return $this->belongsTo(Categoria::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productVariants(){
        return $this->variants->load('optionsValue');
    }

    public function options()
    {
        return $this->hasManyThrough(OptionVariant::class, ProductVariant::class);
    }

    public function optionsValue()
    {

        return  DB::select('select DISTINCT option_values.name, `option_values`.*, `product_variants`.`product_id` from `option_values`
        inner join `option_variants` on `option_variants`.`option_value_id` = `option_values`.`id`
        inner join `product_variants` on `product_variants`.`id` = `option_variants`.`product_variant_id`
        where `product_variants`.`product_id` = ?
        order by option_type_id, position', [$this->id]);
    }

    public function buildDefaultVariants()
    {
        $optionsTypeOne = OptionValue::where('option_type_id', '=', 1)->get();
        $optionsTypeTwo = OptionValue::where('option_type_id', '=', 2)->get();

        foreach ($optionsTypeOne as $optionOne) {

            foreach ($optionsTypeTwo as $optionTwo) {
                $variant = new ProductVariant();

                $variant->product_id = $this->id;
                $variant->sku = $optionOne->presentation . '-' . $optionTwo->presentation;

                $variant->save();

                $optionVariant = new OptionVariant();
                $optionVariant->option_value_id = $optionOne->id;
                $optionVariant->product_variant_id = $variant->id;
                $optionVariant->save();

                $optionVariant = new OptionVariant();
                $optionVariant->option_value_id = $optionTwo->id;
                $optionVariant->product_variant_id = $variant->id;
                $optionVariant->save();
            }
        }
    }
}
