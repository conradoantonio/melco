<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Product;
use \App\OptionValue;
use \App\ProductVariant;
use \App\Sucursal;
use \App\Categoria;
use App\Producto;
use Carbon\Carbon;
use Conekta\Card;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $req)
  {
    //clear product empty
    Product::where('name', '')->where('created_at', '<', Carbon::parse('-1 hours'))->delete();

    $title = $menu = "Productos";
    $items = Product::orderBy('id', 'desc')->get();

    if ($req->ajax()) {
      return view('products.table', compact('items'));
    }
    return view('products.index', compact('items', 'menu', 'title'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($id = 0)
  {
    $title = "Formulario";
    $menu = "Productos";
    $item = null;

    if ($id) {
      $item = Product::find($id);
    } else {
      $item = new Product();

      $item->name = 'Mi producto';

      $item->save();
    }

    $sucursales = Sucursal::all();
    $categorias = Categoria::all();
    $product_variants = [];

    return view('products.form', compact('item', 'menu', 'title', 'sucursales', 'categorias', 'product_variants'));
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $title = "Formulario";
    $menu = "Productos";
    $item = null;
    if ($id) {
      $item = Product::find($id);
    }

    $sucursales = Sucursal::all();
    $categorias = Categoria::all();
    $product_variants = $item->productVariants() ?: [];

    //Log::info('categorias => ' . dd($categorias));

    return view('products.form', compact('item', 'menu', 'title', 'categorias', 'sucursales', 'product_variants'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $req, $id)
  {
    $form = $this->processForm($req);
    $item = Product::find($req->id);

    $variants_id = array_keys($req->product_variants);
    $item_vars = $item->variants()->pluck('id');

    ProductVariant::whereIn('id', $item_vars->diff($variants_id))->delete();

    foreach ($req->product_variants as $id => $variantForm) {

      $variant = ProductVariant::updateOrCreate(['id' => $id],
                                               [
                                                 'product_id' => $item->id,
                                                 'price_sale' => $variantForm['price_sale'],
                                                 'sku'        => $variantForm['sku'],
                                                 'stock'      => $variantForm['stock'],
                                               ]
                                              );

      $options =  json_decode($variantForm['options_value'], true);
      $options_id = [];

      foreach ($options as $opt) {
        $dbOpt = OptionValue::firstOrCreate(['name' => $opt['name']], ['option_type_id' => $opt['option_type_id'], 'presentation' => $opt['name']]);

        array_push($options_id, $dbOpt['id']);
      }

      $variant->optionsValue()->sync($options_id);

      $variant->save();
    }

    $item->fill($form);

    $item->save();

    return response(['msg' => 'Registro guardado exitósamente correctamente', 'url' => url('products/' . $item->id . '/edit'), 'status' => 'success'], 200);
  }

  public function processForm(Request $req)
  {
    $form['name']         = $req->name;
    $form['category_id']  = $req->category_id;
    $form['price_cost']   = $req->price_cost;
    $form['price_weight'] = $req->price_weight;
    $form['location']     = $req->location;
    $form['stock']        = $req->stock;
    $form['description']  = $req->description;

    if ($req->has('id')) {
      $form['id'] = $req->id;
    }

    Log::info('form => ' . print_r($form, true));

    return $form;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }

  public function ajaxImage(Request $request, $id)
  {
    if ($request->isMethod('get'))
      return view('ajax_image_upload');
    else {
      $validator = Validator::make(
        $request->all(),
        [
          'file' => 'image',
        ],
        [
          'file.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg)'
        ]
      );
      if ($validator->fails())
        return array(
          'fail' => true,
          'errors' => $validator->errors()
        );
      $extension = $request->file('file')->getClientOriginalExtension();
      $dir = 'uploads/products/' . $id;
      $filename = uniqid() . '_' . time() . '.' . $extension;
      $request->file('file')->move($dir, $filename);

      Log::info("filename => {$filename}");

      $item = Product::find($id);

      \File::Delete(public_path($item->featured_image));


      $item->featured_image = "{$dir}/{$filename}";

      $item->save();

      return response()->json(['msg' => 'success', 'filename' => "{$request->getSchemeAndHttpHost()}/{$item->featured_image}"]);
    }
  }
}
