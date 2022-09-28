<?php

namespace App\Http\Controllers\API;

use DB;

use \App\User;
use \App\Producto;
use \App\Categoria;
use App\Http\Controllers\Controller;


use \App\Events\RefreshEvent;
use App\OptionValue;
use App\OptionVariant;
use App\Product;
use App\ProductVariant;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class ProductApiController extends Controller
{

    public function list()
    {
        $products = Product::all();

        return response(['status' => 'success', 'data' => $products], 200);
    }

    public function variants(Request $req, $id)
    {
        $item = Product::find($id)->load('variants');

        return response(['status' => 'success', 'data' => $item], 200);
    }

    public function homePageData(Request $req)
    {

        $categories = Categoria::all();
        $cats = [];

        foreach ($categories as $cat) {
            $cats[] = [
                'objectId'   => $cat->id,
                'name'       => $cat->nombre,
                "status"     => "Active",
                "isFeatured" => true,
                'subCategoryCount' => 0,
                '__type'     => "Object",
                'className'  => "Category",
                "canonical"  => $cat->nombre,
                "slug"       => $cat->nombre,
                "image"      => [
                    "__type" => "File",
                    "name" => basename($cat->image),
                    "url"  => "{$req->getSchemeAndHttpHost()}/{$cat->image}"
                ],
                "imageThumb"      => [
                    "__type" => "File",
                    "name" => basename($cat->image),
                    "url"  => "{$req->getSchemeAndHttpHost()}/{$cat->image}"
                ]
            ];
        }

        $items_featured = Product::where('featured', 1)->get();
        $ifeatured = [];

        foreach ($items_featured as $item) {
            $ifeatured[] = $this->arrayItem($item, $req);
        }

        $items_newest = Product::orderBy('created_at', 'desc')->take(30)->get();
        $iNewest = [];

        foreach ($items_newest as $item) {
            $iNewest[] = $this->arrayItem($item, $req);
        }

        return response([
            'result' => [
                'categories'      => $cats,
                'itemsFeatured'   => $ifeatured,
                'itemsNewArrival' => $iNewest,
                'brands'          => [],
                'itemsOnSale'     => [],
                'slides'          => [],
            ]
        ], 200);
    }

    public function getOptionValuesByType(Request $req)
    {

        $data = $req->json()->all();

        //Log::info('getOptionValuesByType => ' .  $data);

        $product = Product::find($req->product_id);

        //dd($product->productVariant());

        $variant_ids = $product->variants->pluck('id');

        $product_option_value_ids = OptionVariant::whereIn('product_variant_id', $variant_ids)->get()->unique('option_value_id')->pluck('option_value_id');

        //dd($product_option_value_ids);

        $options = OptionValue::select('name', 'presentation as text', 'id', 'position', 'option_type_id')->where('option_type_id', $req->type)->get();

        $options->each(function ($item, $key) use ($product_option_value_ids) {
            if ($product_option_value_ids->contains($item->id)) {
                return $item['selected'] = true;
            }
          //return strtoupper($item['name']);
        });

        //dd($options);

        return \Response::json($options);
    }

    public function getItem(Request $req)
    {
        $data = $req->json()->all();

        //Log::info('datapyload => ' . $data['where']['objectId']);

        $res = [];

        if(isset($data['where']['objectId']) || isset($data['id'])){
            $id = isset($data['id']) ? $data['id'] : $data['where']['objectId'];

            $prod = Product::find($id)->load('variants', 'category');

            $res[] = $this->arrayItem($prod, $req);
        } else {
            return $this->getItems($req);
        }

        //dd($res);
        Log::info('res => ' . json_encode($res));

        return response(['results' => $res], 200);
    }

    public function getItems(Request $req)
    {
        $data = $req->json()->all();

        $page = $req->page ? $req->page : 1;
        $products = [];

        $res = 'results';

        if (isset($data['where']['category'])) {

            Log::info('category_id => ' . $data['where']['category']['objectId']);

            $products = Product::where('category_id', $data['where']['category']['objectId'])->paginate(25);

        } elseif(isset($data['where'])){
            $products = Product::paginate(25);
        } else {

            $products = Product::paginate(25);
            $res = 'result';
        }

        $items = [];

        foreach ($products as $item) {
            $items[] = $this->arrayItem($item, $req);
        }

        return response([$res => $items], 200);
    }

    public function arrayItem(Product $item, Request $req)
    {

        Log::info('variants' . count($item->variants));

        return [
          'id'                 => $item->id,
          'objectId'           => $item->id,
          'name'               => $item->name,
          'canonical'          => $item->name,
          'description'        => nl2br($item->description),
          'featuredImage'      => [ '__type' => "File", 'name' => basename($item->featured_image), 'url' => "{$req->getSchemeAndHttpHost()}/{$item->featured_image}"],
          'featuredImageThumb' => [ '__type' => "File", 'name' => basename($item->featured_image), 'url' => "{$req->getSchemeAndHttpHost()}/{$item->featured_image}"],
          'images'             => [['__type' => "File", 'name' => basename($item->featured_image), 'url' => "{$req->getSchemeAndHttpHost()}/{$item->featured_image}"]],
          'price'              => 7,
          'slug'               => $item->name,
          'variations'         => $item->productVariants(),
          'options'            => $item->optionsValue(),
          'category'           => $item->category,
          'isFeatured'         => true,
          'likeCount'          => 2,
          'likes'              => ['__type' => "Relation", 'className' => "_User"],
          'netPrice'           => $item->price_sale,
          'price'              => @$item->variants[0]->price_sale,
          'ratingAvg' => 0,
          'ratingCount' => 0,
          'ratingTotal' => 0,
          'relatedItems' => [],
          'slug' => $item->name,
          'status' => "Active",
          'tags' => [],
          'ACL' => ['*' => ['read' => true], 'role:Admin' => ['write' => true]],
          'brand' => null,
          'views' => 0,
          'discount' => 0,
        ];
    }

    /**
     * Show the info of multiple articles
     *
     */
    public function searchProduct(Request $req)
    {
        $data = Product::query();

        if ( $req->category_id ) { $data = $data->where('category_id', $req->category_id); }

        if ( $req->search !== null ) {
            $data = $data->where('name', 'like', '%'.$req->search.'%');
        }

        if ( $req->page ) {
            $offset = ( $req->limit ? ( ((int)$req->page - 1) * (int)$req->limit) : 0 );

            $data = $data->offset($offset); 
        }

        if ( $req->limit ) { $data = $data->limit($req->limit); }

        if ( $req->order_by == 'precio_mayor_menor' ) { $data = $data->orderBy('price_weight', 'desc'); }
        elseif ( $req->order_by == 'precio_menor_mayor' ) { $data = $data->orderBy('price_weight', 'asc'); }

        $data = $data->orderBy('featured', 'desc')->get();
        
        if (! count( $data ) ) { return response(['msg' => 'No se encontraron registros con el criterio de búsqueda deseado', 'status' => 'error'], 200); }

        $items = [];

        foreach ($data as $product) {
            $items[] = $this->arrayItem( $product, $req );
        }

        return response(['msg' => 'Registros enlistados a continuación', 'status' => 'success', 'data' => $items], 200);
    }
}
