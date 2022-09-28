<?php

namespace App\Http\Controllers;

use DB;
use Hash;

use \App\Faq;
use \App\User;
use \App\Banner;
use \App\Pedido;
use \App\Producto;
use \App\Categoria;
use \App\Configuration;
use \App\DetallePedido;

use \App\Events\RefreshEvent;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Sign up a new customer (Login)
     *
     * @param  Request $req
     * @return $customer
     */
    public function signUpCustomer(Request $req)
    {
        //If customer already exist check if is registered with some social network
        if ( count(User::user_by_email($req->email)) ) {
            //If its a social network, means that user does not need to registered.
            if ( $req->social_network ) {
                $customer = User::where('email', $req->email)
                ->where('social_network', '!=', 0)//Facebook or google
                ->where('role_id', 3)//Customer
                ->first();

                if ( $customer ) {
                    $customer->role;

                    return response(['msg' => 'Usuario logueado correctamente', 'status' => 'success', 'data' => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at'])], 200);
                }
                return response(['msg' => 'Inicio de sesión inválido, verifique sus datos porfavor', 'status' => 'error'], 200);
            }
            return response(['msg' => 'Éste correo ya está siendo utilizado, porfavor, elija uno diferente', 'status' => 'error'], 200);
        } else {

            $res = $this->saveOpenpayCustomer($req);
            if ( $res['status'] != 'success' ) { return response($res, 200); }#Customer wasn't created on openpay

            $customer = new User;

            if (! $req->social_network ) {
                $customer->password = bcrypt($req->password);
            }
            $customer->fullname = $req->fullname;
            $customer->email = $req->email;
            $customer->photo = 'img/users/default.jpg';
            $customer->social_network = $req->social_network;
            $customer->player_id = $req->player_id;
            $customer->role_id = 3;//Role customer
            $customer->openpay_id = $res['data']->id;

            $customer->save();

            $customer->role;

            return response(['msg' => 'Usuario registrado correctamente', 'status' => 'success', 'data' => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at'])], 200);
        }
    }

    /**
     * Logout customer
     *
     * @param  Request  $request
     * @return response json
     */
    public function logoutCustomer(Request $req)
    {
        $user = User::where('role_id', 3)->where('id', $req->user_id)->first();

        if (! $user ) { return response(['msg' => 'Usuario inválido', 'status' => 'error'], 200); }

        $user->player_id = null;

        $user->save();

        return response(['msg' => 'Usuario deslogueado correctamente', 'status' => 'success'], 200);
    }
    
    
    /**
     * Get info about an user
     *
     * @param  Request  $request
     * @return response json
     */
    public function myProfile(Request $req)
    {
        $user = User::where('role_id', 3)->where('id', $req->user_id)->first();

        if (! $user ) { return response(['msg' => 'Usuario inválido', 'status' => 'error'], 200); }

        return response(['msg' => 'Perfil de usuario encontrado', 'status' => 'success', 'data' => $user->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at'])], 200);
    }
    
    /**
     * Customer login
     *
     * @param  Request  $request
     * @return response json if credentials are correct and status is active (1)
     */
    public function signInCustomer(Request $req)
    {
        $customer = User::where('email', $req->email)
        ->where('status', 1)
        ->where('role_id', 3)
        ->first();
        if ( $customer ) {
            if ( Hash::check( $req->password, $customer->password ) ) {
                //$this->check_in($customer->id);
                $customer->role;

                if ( $req->player_id ) { 
                    $customer->player_id = $req->player_id;

                    $customer->save();
                }
                return response(['msg' => 'Inicio de sesión correcto', 'status' => 'success', 'data' => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at'])], 200);
            }
            return response(['msg' => 'Contraseña errónea', 'status' => 'error'], 200);
        }
        return response(['msg' => 'Correo inválido', 'status' => 'error'], 200);
    }

    /**
     * Actualiza el usuario
     *
     * @param  Request  $request
     * @return response json if credentials are correct and status is active (1)
     */
    public function updateUser(Request $req)
    {
        $item = User::where('id', $req->user_id)
        ->where('status', 1)
        ->where('role_id', 3)
        ->first();

        if (! $item ) { return response([ 'msg' => "Este correo no pertenece a ninguna cuenta asociada", 'status' => 'error'], 200); }

        $img = $this->upload_file($req->file('photo'), 'img/users', true);

        $img ? $item->photo = $img : '';

        $req->password ? $item->password = bcrypt($req->password) : '';
        $req->phone ? $item->phone = $req->phone : '';
        $req->fullname ? $item->fullname = $req->fullname : '';
        $req->social_network ? $item->social_network = $req->social_network : '';
        $req->player_id ? $item->player_id = $req->player_id : '';

        
        $item->save();
        
        return response(['msg' => 'Usuario actualizado correctamente', 'status' => 'success', 'data' => $item], 200);
    }
    
    /**
     * Send an email with a new password
     *
     * @return view mail
     */
    public function recoverPassword(Request $req)
    {
        $item = User::where(['email' => $req->email])->where('role_id', 3)->first();
        
        if (! $item ) { return response([ 'msg' => "Este correo no pertenece a ninguna cuenta asociada", 'status' => 'error'], 200); }
        
        if ( $item->role_id != 2 && $item->role_id != 3 ) { return response([ 'msg' => "No se puede restablecer la contraseña, lo sentimos", 'status' => 'error'], 200); }
        
        $pass = str_random(6);
        
        $item->password = bcrypt( $pass );

        if ( $item ) {
            $newPass = str_random(8);

            $item->password = bcrypt( $newPass );
            $item->save();

            $params = array();

            $params['view'] = 'mails.reset_password';
            $params['subject'] = 'Cambio de contraseña';
            $params['user'] = $item;
            $params['email'] = $item->email;
            $params['password'] = $newPass;

            $this->f_mail( $params );
            
            return response(['msg' => 'Correo enviado exitósamente', 'status' => 'success'], 200);
        }

        return response(['msg' => 'Ocurrió un error tratando de enviar el correo, trate nuevamente', 'status' => 'error'], 200);
    }

    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function processOrder(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido o no encontrado', 'status' => 'error'], 200); }
        
        #Try to make payment
        $res = $this->payOrder( $user, round( $req->amount, 2 ), null, $req );
        if ( $res['status'] == 'success' ) {
            $order = $res['data'];

            $item = New Pedido;

            $item->id_sucursales = 1;
            $item->id_users = $user->id;
            $item->id_status_pedido = 1;
            $item->id_tipo_pedido = $req->id_tipo_pedido;
            $item->id_tipo_pago = 1;
            $item->token_orden = $order->id;
            $item->total = $order->amount;//Total paid by customer
            $item->sub_total = $order->amount;
            
            $item->save();

            //Create shipment and get the best provider
            
                        
            //Create and generate Skydropx Label 

            foreach ($req->products as $product) {
                $det = New DetallePedido;

                $det->id_pedido = $item->id;
                $det->id_producto = $product['id'];
                $det->nombre_producto = $product['nombre_producto'];
                $det->cantidad = $product['cantidad'];
                $det->precio_u = $product['precio_u'];
                $det->total = $product['cantidad'] * $product['precio_u'];

                $det->save();
            }
        }

        return response($res, 200);
    }

    /**
     * Updates the player id for onesignal
     *
     * @return json
     */
    public function updatePlayerid(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'Usuario no encontrado.', 'status' => 'error'], 200); }

        $user->player_id = $req->player_id;

        $user->save();

        return response(['msg' => 'Player ID modificado con éxito', 'status' => 'success'], 200);
    }

    /**
     * Display the banners
     *
     * @return \Illuminate\Http\Response
     */
    public function getBanners(Request $req)
    {
        if ( $req->has('type') ) { $data = Banner::where('type', $req->type)->get(); }
        else { $data = Banner::all(); }
        
        if ( count( $data ) ) {
            return response(['msg' => 'Banners enlistados a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay banners por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Get the faqs
     *
     * @return \Illuminate\Http\Response
     */
    public function getFaqs()
    {
        $data = Faq::all();

        if ( count( $data ) ) {
            return response(['msg' => 'Preguntas frecuentes enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay preguntas frecuentes por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Get the categories
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategories()
    {
        $data = Categoria::all();

        if ( count( $data ) ) {
            return response(['msg' => 'Categorías enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay categorías por mostrar', 'status' => 'error'], 200);
    }
}
