<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#Root url
Route::get('/', function () {
	return auth()->check() ? redirect()->action('LoginController@load_dashboard') : view('login');
});

#Login view
Route::get('login', function () {
    return view('login');
})->name('login');

#Mail view
Route::get('reset-preview', function () {
    return view('mails.reset_password');
});

#Logout url
Route::get('logout', 'LoginController@logout');

#Sign up url
Route::get('sign-up', 'LoginController@sign_up');
#Route::post('register', 'CustomersController@save');


Route::get('recuperar-cuenta', 'LoginController@resetView');

#Push url test
Route::get('test', function() {
    event(new App\Events\PusherEvent('Hi there Pusher!'));
    return 'Event sent';
});

#Route url
Route::post('login', 'LoginController@index');

#Admin url
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', 'LoginController@load_dashboard')->middleware('role:Administrador');

    #Multitask controller
    Route::middleware(['role:Administrador'])->prefix('system')->group(function () {
        Route::post('upload-content', 'MultiTaskController@uploadFile');
    });

    #Mi perfil
    Route::middleware(['role:Administrador,Usuario'])->prefix('mi-perfil')->group(function () {
        Route::get('/', 'MiPerfilController@index');
        Route::post('update', 'MiPerfilController@update');
    });

    #Sucursales
    Route::middleware(['role:Administrador'])->prefix('sucursales')->group(function () {
        Route::get('/', 'SucursalesController@index');
        Route::get('form/{id?}', 'SucursalesController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
        Route::post('save', 'SucursalesController@save');
        Route::post('update', 'SucursalesController@update');
        Route::post('delete', 'SucursalesController@delete');
    });

    #Pedidos
    Route::middleware(['role:Administrador,Usuario'])->prefix('pedidos')->group(function () {
        Route::get('/', 'PedidosController@index');
        Route::get('excel/export', 'PedidosController@export');
        Route::get('detalles/{id}', 'PedidosController@showInfo')->name('pedidos.supermarket.info');
        Route::get('get-printable/{id}', 'PedidosController@getPrintable');
        Route::post('send-email', 'PedidosController@sendOrderToCustomer');
        Route::post('filter', 'PedidosController@filter');
        Route::post('change-status', 'PedidosController@changeStatus');
        Route::post('cancel', 'PedidosController@cancellOrder');
        Route::post('refund', 'PedidosController@refundOrder');
    });

    #Órdenes
    Route::middleware(['role:Administrador,Usuario'])->prefix('ordenes')->group(function () {
        Route::get('/', 'OrdenesController@index');
        Route::get('excel/export', 'OrdenesController@export');
        Route::get('detalles/{id}', 'OrdenesController@showInfo');
        Route::get('get-printable/{id}', 'OrdenesController@getPrintable');
        Route::post('send-email', 'OrdenesController@sendOrderToCustomer');
        Route::post('filter', 'OrdenesController@filter');
        Route::post('change-status', 'OrdenesController@changeStatus');
        Route::post('cancel', 'OrdenesController@cancellOrder');
        Route::post('refund', 'OrdenesController@refundOrder');
    });

    #Users CRUD
    Route::middleware(['role:Administrador'])->prefix('usuarios')->group(function () {
        #System
        Route::prefix('sistema')->group(function () {
            Route::get('/', 'UsersController@index');
            Route::get('form/{id?}', 'UsersController@form');
        });

        #Franchisees
        Route::prefix('franquiciatarios')->group(function () {
            Route::get('/', 'FranquiciatariosController@index');
            Route::get('form/{id?}', 'FranquiciatariosController@form');
            Route::get('excel/export', 'FranquiciatariosController@export');
            Route::post('filter', 'FranquiciatariosController@filter');
            Route::post('save', 'FranquiciatariosController@save');
            Route::post('update', 'FranquiciatariosController@update');
            Route::post('delete', 'FranquiciatariosController@delete');
            Route::post('change-status', 'FranquiciatariosController@changeStatus');
        });

        #Customers
        Route::prefix('clientes')->group(function () {
            Route::get('/', 'ClientesController@index');
            Route::get('form/{id?}', 'ClientesController@form');
            Route::get('excel/export', 'ClientesController@export');
            Route::post('filter', 'ClientesController@filter');
            Route::post('save', 'ClientesController@save');
            Route::post('update', 'ClientesController@update');
            Route::post('delete', 'ClientesController@delete');
            Route::post('change-status', 'ClientesController@changeStatus');
        });

        Route::post('save', 'UsersController@save');
        Route::post('update', 'UsersController@update');
        Route::post('delete', 'UsersController@delete');
        Route::post('change-status', 'UsersController@change_status');
    });

    #Products CRUD

    Route::middleware(['role:Administrador'])->group(function(){
        Route::resource('products', 'ProductController');

        Route::prefix('products')->group(function(){
            Route::post('{id}/image-upload', 'ProductController@ajaxImage');
        });
        //Route::resource('variants', 'ProductVariantController');
    });

    #Formulario de contacto
    Route::middleware(['role:Administrador'])->prefix('formulario-de-contacto')->group(function () {
        Route::get('/', 'QuestionsController@index');
        Route::get('form/{id?}', 'QuestionsController@form');
        Route::post('filter', 'QuestionsController@filter');
        Route::post('save', 'QuestionsController@save');
        Route::post('update', 'QuestionsController@update');
        Route::post('delete', 'QuestionsController@delete');
        Route::post('change-status', 'QuestionsController@changeStatus');
    });

    Route::middleware(['role:Administrador,Usuario'])->prefix('productos')->group(function () {
        Route::prefix('fastfood')->group(function () {
            Route::get('/{id?}', 'ProductosFastFoodController@index')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric
            Route::get('form/{id?}', 'ProductosFastFoodController@form');
            Route::get('get-galery/{id}', 'ProductosFastFoodController@getGalery');
            Route::get('excel/export', 'ProductosFastFoodController@export');
            Route::post('excel/import', 'ProductosFastFoodController@import');
            Route::post('save', 'ProductosFastFoodController@save');
            Route::post('update', 'ProductosFastFoodController@update');
            Route::post('delete', 'ProductosFastFoodController@delete');
            Route::post('show-info', 'ProductosFastFoodController@showInfo');
            Route::post('filter', 'ProductosFastFoodController@filter');
            Route::post('upload-content', 'ProductosFastFoodController@uploadContent');
            Route::post('delete-content', 'ProductosFastFoodController@deleteContent');
            Route::post('change-stock', 'ProductosFastFoodController@changeStock');
            /*Route::post('change-status', 'ProductosFastFoodController@change_status');*/
        });

        Route::prefix('supermarket')->group(function () {
            Route::get('/{id?}', 'ProductosSupermarketController@index')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric
            Route::get('form/{id?}', 'ProductosSupermarketController@form');
            Route::get('get-galery/{id}', 'ProductosSupermarketController@getGalery');
            Route::get('excel/export', 'ProductosSupermarketController@export');
            Route::post('excel/import', 'ProductosSupermarketController@import');
            Route::post('save', 'ProductosSupermarketController@save');
            Route::post('update', 'ProductosSupermarketController@update');
            Route::post('delete', 'ProductosSupermarketController@delete');
            Route::post('show-info', 'ProductosSupermarketController@showInfo');
            Route::post('filter', 'ProductosSupermarketController@filter');
            Route::post('upload-content', 'ProductosSupermarketController@uploadContent');
            Route::post('delete-content', 'ProductosSupermarketController@deleteContent');
            Route::post('change-stock', 'ProductosSupermarketController@changeStock');
            /*Route::post('change-status', 'ProductosSupermarketController@change_status');*/
        });

    });

    #Artículos de tecnología, muebles, etc
    Route::middleware(['role:Administrador'])->prefix('articulos')->group(function () {
        Route::get('/', 'ArticulosController@index');
        Route::get('form/{id?}', 'ArticulosController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
        Route::get('get-galery/{id}', 'ArticulosController@getGalery');
        Route::post('save', 'ArticulosController@save');
        Route::post('update', 'ArticulosController@update');
        Route::post('delete', 'ArticulosController@delete');
        Route::post('change-status', 'ArticulosController@changeStatus');
        Route::post('filter', 'ArticulosController@filter');
        Route::get('excel/export', 'ArticulosController@export');
    });

    #Preguntas frecuentes
    Route::middleware(['role:Administrador'])->prefix('preguntas-frecuentes')->group(function () {
        Route::get('/', 'FaqsController@index');
        Route::get('form/{id?}', 'FaqsController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
        Route::post('save', 'FaqsController@save');
        Route::post('update', 'FaqsController@update');
        Route::post('delete', 'FaqsController@delete');
        Route::post('change-status', 'FaqsController@changeStatus');
    });
    
    #Categorías
    Route::middleware(['role:Administrador'])->prefix('categorias')->group(function () {
        Route::get('/', 'CategoriasController@index');
        Route::get('form/{id?}', 'CategoriasController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
        Route::post('save', 'CategoriasController@save');
        Route::post('update', 'CategoriasController@update');
        Route::post('delete', 'CategoriasController@delete');
        Route::post('change-status', 'CategoriasController@ch<angeStatus');
        Route::post('{id}/image-upload', 'CategoriasController@ajaxImage');
    });

    #Preguntas frecuentes
    Route::middleware(['role:Administrador'])->prefix('preguntas-frecuentes')->group(function () {
        Route::get('/', 'FaqsController@index');
        Route::get('form/{id?}', 'FaqsController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
        Route::post('save', 'FaqsController@save');
        Route::post('update', 'FaqsController@update');
        Route::post('delete', 'FaqsController@delete');
        Route::post('change-status', 'FaqsController@changeStatus');
    });

     #Preguntas frecuentes
    Route::middleware(['role:Administrador'])->prefix('skydropx-guides')->group(function () {
        Route::get('/', 'SkydropxController@index');
        Route::get('form/{id?}', 'SkydropxController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
        Route::post('save', 'SkydropxController@save');
        Route::post('update', 'SkydropxController@update');
        Route::post('delete', 'SkydropxController@delete');
        Route::post('change-status', 'SkydropxController@changeStatus');
    });

    #Notificaciones push
    Route::middleware(['role:Administrador'])->prefix('notificaciones-push')->group(function () {
        Route::get('/', 'NotificacionesPushController@index');
        Route::post('send', 'NotificacionesPushController@sendPush');
        Route::post('filter', 'NotificacionesPushController@filterUsers');
    });

    #Configuación
    Route::middleware(['role:Administrador'])->prefix('configuracion')->group(function () {
        Route::get('politicas-de-privacidad', 'ConfigurationController@index');
        Route::post('save/terms-and-conditions', 'ConfigurationController@save_terms_conditions');

        #Sucursales
        Route::middleware(['role:Administrador'])->prefix('banners')->group(function () {
            Route::get('/', 'BannersController@index');
            Route::get('form/{id?}', 'BannersController@form')->where('id', '[0-9]+');//Force to redirect to index only when param is numeric;
            Route::post('save', 'BannersController@save');
            Route::post('update', 'BannersController@update');
            Route::post('delete', 'BannersController@delete');
            Route::post('change-status', 'BannersController@changeStatus');
        });
    });
});

#Api
/*
Route::prefix('api/v1')->group(function () {
    Route::post('sign-up-customer', 'ApiController@signUpCustomer');
    Route::post('login-customer', 'ApiController@signInCustomer');
    Route::post('recover-password', 'ApiController@recoverPassword');
    Route::post('update-player-id', 'ApiController@updatePlayerid');
    Route::get('get-banners', 'ApiController@getBanners');
    Route::get('get-faqs', 'ApiController@getFaqs');
    Route::get('get-categories', 'ApiController@getCategories');
});
*/
