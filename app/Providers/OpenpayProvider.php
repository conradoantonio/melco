<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Openpay;
use OpenpayApi;

class OpenpayProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  {
    //
  }

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton(OpenpayApi::class, function ($app) {
      return Openpay::getInstance(config('services.openpay.id'), config('services.openpay.sk'));
    });
  }
}
