<?php

namespace App\Http\Controllers\API;

use DB;

use \App\User;
use \App\Producto;
use \App\Categoria;
use \App\DetallePedido;
use App\Http\Controllers\Controller;


use \App\Events\RefreshEvent;
use App\OptionValue;
use App\Pedido;
use App\Product;
use App\ProductVariant;
use App\StatusPedido;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class CategoryApiController extends Controller
{
  public function categories(Request $req){

    $categories = Categoria::all();
    $res = [];

    foreach ($categories as $cat) {
      $res[] = $this->array_category($cat, $req);
    }

    return response(['results' => $res], 200);
  }

  public function getCategory(Request $req){
    $data = $req->json()->all();

    $id = isset($data['id']) ? $data['id'] : $data['where']['category']['objectId'];

    $cat = Categoria::find($id);

    $res = $this->array_category($cat, $req);

    return response(['results' => $res], 200);
  }

  public function array_category($category, $req){
    return [
      "objectId" => $category->id,
      "name" => $category->name,
      "status" => "Active",
      "isFeatured" => true,
      "image" => [
        "__type" => "File",
        "name" => basename($category->image),
        'url' => "{$req->getSchemeAndHttpHost()}/{$category->image}",
        'canonical' => $category->name,
        'slug' => $category->name,
      ],
      'canonical' => $category->name,
      'slug' => $category->name,
      'imageThumb' => [
        "__type" => "File",
        "name" => basename($category->image),
        'url' => "{$req->getSchemeAndHttpHost()}/{$category->image}",
      ],
      'subCategoryCount' => 0,
    ];
  }
}
