<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use \App\Articulo;
use \App\TipoArticulo;

class ArticlesController extends Controller
{
    /**
    * Get the categories
    *
    * @return \Illuminate\Http\Response
    */
    public function getTypeArticle(Request $req)
    {
      $data = TipoArticulo::all();

      if ( count( $data ) ) {
          return response(['msg' => 'Tipo de artículos enlistados a continuación', 'status' => 'success', 'data' => $data], 200);
      }

      return response(['msg' => 'No hay registros por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Show the questions for one user
     *
     */
    public function getArticles(Request $req)
    {
        $data = Articulo::filter_rows($req->limit, $req->page, $req->tipo_id, $req->search, $req->order_by);

        if ( count( $data ) ) {
            return response(['msg' => 'Registros enlistados a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay registros por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Show the info of a single article
     *
     */
    public function getArticleDetail(Request $req)
    {
        $item = Articulo::find($req->articulo_id);

        if ( $item ) {
            return response(['msg' => 'Articulo enlistado a continuación', 'status' => 'success', 'data' => $item], 200);
        }

        return response(['msg' => 'No se encontró el artículo deseado', 'status' => 'error'], 200);
    }

    /**
     * Show the info of multiple articles
     *
     */
    public function getArticleData(Request $req)
    {
        if ( is_array($req->articulos_id) ) {
            $item = Articulo::whereIn('id', $req->articulos_id)->get();

            if ( $item ) {
                return response(['msg' => 'Articulo enlistado a continuación', 'status' => 'success', 'data' => $item], 200);
            }
        } else {
            return response(['msg' => 'Formato de ids inválido, envíe un arreglo de ids numérico válido', 'status' => 'error'], 200);
        }

        return response(['msg' => 'No se encontró el artículo deseado', 'status' => 'error'], 200);
    }
}
