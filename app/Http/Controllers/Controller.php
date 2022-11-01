<?php

namespace App\Http\Controllers;

use App\Traits\OpenpayMethods;
use App\Traits\GeneralFunctions;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use Openpay;
use OpenpayApiError;
use OpenpayApiRequestError;
use OpenpayApiResourceBase;
use OpenpayApiTransactionError;

// require('../vendor/openpay/sdk/Openpay/Resources');

require_once '../vendor/autoload.php';

// require('../vendor/openpay/sdk/Openpay/Openpay.php');

setlocale(LC_ALL,'es_ES', 'esp_esp');

class Controller extends BaseController
{   
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, GeneralFunctions, OpenpayMethods;

	#Declare a middleware in the construct, so we can access to the current user!
	function __construct() {
        date_default_timezone_set('America/Mexico_City');
        $this->summer = date('I');
        $this->actual_date = date('Y-m-d');
        $this->actual_month = date('Y-m');
        $this->actual_datetime = date('Y-m-d H:i:s');
        $this->app_id = "5e6822ca-f71d-4fa7-be23-2829e8650f13";
        $this->app_key = "NmE4MTUwNWUtMjcyNy00OWM1LWE1MzQtOTI1NmRjZmYzYjc4";
        $this->app_icon = asset("img/icon_customer.png");

        $this->middleware(function ($request, $next) {
            $this->current_user = auth()->user();

            return $next($request);
        });
	}

    public function home(Request $req){ 
        Openpay::setId(env('OPENPAY_MERCHANT_ID'));
        Openpay::setApiKey(env('OPENPAY_API_KEY'));
        Openpay::setProductionMode(env('OPENPAY_PRODUCTION_MODE'));

        return $this->openpay = Openpay::getInstance();
    }
}
