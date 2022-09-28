<?php

namespace App\Http\Controllers\API;

use \App\Banner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BannersController extends Controller
{
    /**
     * Show the banners for the app
     *
     */
    public function getBanners()
    {
        $data = Banner::inRandomOrder()->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Banners enlistados a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay banners por mostrar', 'status' => 'error'], 200);
    }
}
