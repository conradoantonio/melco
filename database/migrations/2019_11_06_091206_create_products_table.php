<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('products');

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('sub_category_id')->unsigned();
            $table->string('name');
            $table->string('description');
            $table->integer('size')->unsigned();
            $table->string('location');
            $table->boolean('featured');
            $table->double('price_weight');
            $table->double('price_cost');
            $table->double('price_sale');
            $table->integer('stock');
            $table->string('provider');
            $table->text('featured_image');
            $table->text('images');

            //$table->foreign('category_id')->references('id')->on('categorias');
            //$table->foreign('sub_category_id')->references('id')->on('categorias');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
