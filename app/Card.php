<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Openpay;
use OpenpayApi;

class Card extends Model{



  static function cards(){
    Log::info('on cards => ' . dd(resolve(OpenpayApi::class)->customers->get('afanlxukdwypypyjlrtz0')));
    //return $this->openpay->customers->get('afanlxukdwypypyjlrtz');
  }
}
