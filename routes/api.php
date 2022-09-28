<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

#Endpoints para registro, inicio de sesión y recuperar contraseñas viejos
#Route::post('login', 'API\UserController@signInCustomer');
#Route::post('signUp', 'API\UserController@signUpCustomer');
#Route::post('recoverPassword', 'API\UserController@recoverPassword');

#SkyDropx
Route::get('/skydropx/labels', 'SkydropxController@labels');
Route::post('/skydropx/quotations', 'SkydropxController@quotations');
Route::post('/skydropx/shipments', 'SkydropxController@shipments');

#
Route::post('sign-up', 'ApiController@signUpCustomer');
Route::post('login-app', 'ApiController@signInCustomer');
Route::post('logout', 'ApiController@logoutCustomer');
Route::post('recover-password', 'ApiController@recoverPassword');
Route::post('update-user', 'ApiController@updateUser');
Route::post('my-profile', 'ApiController@myProfile');

//Route::middleware('auth:api')->post('users/update', 'API\UserController@update');
Route::middleware('auth:api')->post('users', 'API\UserController@update');

Route::post('files/photo.jpg', 'API\UserController@photo');

Route::post('/functions/getHomePageData', 'API\ProductApiController@homePageData');
Route::post('/functions/getItems', 'API\ProductApiController@getItems');
Route::post('/classes/Search', 'API\ProductApiController@searchProduct');
Route::post('/classes/Item', 'API\ProductApiController@getItem');
Route::post('/classes/Cart', 'API\CartApiController@getCart');
Route::post('/classes/Cart/{id}', 'API\CartApiController@updateCart');
Route::post('/classes/Category', 'API\CategoryApiController@categories');
Route::post('/classes/Card', 'API\UserController@card');
Route::post('/classes/Card/{id}', 'API\UserController@cardDestroy');
Route::post('/classes/Order', 'API\CartApiController@order');
Route::post('/classes/pay-order', 'API\PedidoController@payOrderProducts');
Route::post('/functions/item', 'API\ProductApiController@getItem');

Route::get('/categories', 'API\ApiController@categories');
Route::get('/products', 'API\ProductApiController@list');
Route::get('/products/{id}/variants', 'API\ProductApiController@variants');

Route::get('products/options', 'API\ProductApiController@getOptionValuesByType');

#Update 2.0
#Preguntas frecuentes normales

Route::get('/faqs', 'API\ApiController@faqs');
#Obtiene las categorías (tipos) de artículos
Route::get('/articles-type', 'API\ArticlesController@getTypeArticle');
Route::post('/articles', 'API\ArticlesController@getArticles');
Route::post('/articles/data', 'API\ArticlesController@getArticleData');
Route::post('/article/', 'API\ArticlesController@getArticleDetail');
Route::post('/classes/Banner', 'API\BannersController@getBanners');

#Preguntas que se adjudican directo al administrador
Route::post('/get-questions', 'API\QuestionsController@getQuestions');
Route::post('/save-question', 'API\QuestionsController@saveQuestion');
Route::post('/delete-question', 'API\QuestionsController@deleteQuestion');

#Tarjetas
Route::post('/get-cards', 'API\CardsController@getCards');
Route::post('/save-card', 'API\CardsController@save');
Route::post('/delete-card', 'API\CardsController@delete');

#Direcciones
Route::post('/get-addresses', 'API\AddressesController@getAddresses');
Route::post('/save-address', 'API\AddressesController@save');
Route::post('/delete-address', 'API\AddressesController@delete');

#Pedidos
Route::get('/shipping-cost', 'API\ApiController@shippingCost');
Route::post('/get-orders', 'API\ApiController@getOrders');
Route::post('/get-order-detail', 'API\ApiController@getOrderDetail');
Route::post('/create-order', 'API\ApiController@processOrder');
Route::post('/change-status-order', 'API\ApiController@changeStatusOrder');

#Términos y condiciones
Route::get('get-legal-info', 'API\ApiController@getLegalInfo');
Route::post('spei-webhook', 'API\ApiController@speiWebhook');
