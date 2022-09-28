<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'det_pedido';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pedido_id', 'producto_id', 'product_variant_id', 'nombre_variante', 'nombre_producto', 'cantidad', 'precio_u', 'total'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the order related to the record
     *
     */
    public function pedido()
    {
        return $this->belongsTo('App\Pedido', 'id_pedido');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'producto_id');
    }

    public function productVariant(){
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_id');
    }

    public function getTotalAttribute(){
        return $this->cantidad * $this->precio_u;
    }

    /**
     * Get the supermarket products related to the record
     *
     */
    public function productosSupermarket()
    {
        return $this->hasOne('App\ProductoSuper', 'art_id', 'id_producto');
    }

    /**
     * Get the fastfood products related to the record
     *
     */
    public function productosFastFood()
    {
        return $this->hasOne('App\ProductoFastFood', 'art_id', 'id_producto');
    }
}
