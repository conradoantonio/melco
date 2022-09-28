<?php

namespace App\Traits;

use DB;
use Mail;
use Image;

use \App\User;
use \App\Coupon;
use \App\Singin;
use \App\Product;
use \App\Articulo;
use \App\Business;
use \App\ProductVariant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait GeneralFunctions
{
    /**
     * Verify if a file is valid, then upload it to a given path.
     *
     * @return $name
     */
    public function upload_file($file, $path, $rename = false, $resize = false)
    {
        $extensions = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png", "4"=>"gif", "5" => "pdf");
        $name = '';

        if ( $file ) {
            $file_ext = $file->getClientOriginalExtension();
            if (array_search($file_ext, $extensions)) {
                if (! File::exists( $path ) ) {
                    File::makeDirectory(public_path().'/'.$path, 0755, true, true);
                }

                $name = $rename ? $path.'/'.time().'.'.$file_ext : $path.'/'.$file->getClientOriginalName();

                if ( is_array( $resize) ) {
                    $content = Image::make( $file )
                    ->resize( $resize['width'], $resize['height'] )
                    ->save( $name );
                } else {
                    $file->move($path, $name);
                }
                
                return $name;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Get a random string
     *
     * @return strtoupper($codigo)
     */
    public function get_random_code($size = 8)
    { 
        $code = '';
        $exist = [true];
        while ( count( $exist ) ) {
            $code = strtoupper( str_random( $size ) );
            $exist = Coupon::where('code', $code)->get();
        }
        return $code;
    }

    /**
     * Get a random string
     *
     * @return strtoupper($codigo)
     */
    public function getNotifcationsUSers(Request $req)
    {
        $verifyPlayerID = null;
        $customers = [];

        if ( $req->filter == 'all' ) { $customers = User::filter_rows(auth()->user(), [3], null, null, $verifyPlayerID); }
        elseif ( $req->filter == 'top_users' ) { $customers = User::getTopTen([3], null, $verifyPlayerID); }
        elseif ( $req->filter == 'enabled_users' ) { $customers = User::filter_rows(auth()->user(), [3], '1', null, $verifyPlayerID); }
        elseif ( $req->filter == 'disabled_users' ) { $customers = User::filter_rows(auth()->user(), [3], '0', null, $verifyPlayerID); }

        return ['data' => $customers, 'msg' => 'Usuarios enlistados a continución', 'status' => 'success'];
    }

    /**
     * Send a notification to a single user or a group of users.
     *
     * @return $name
     */
    public function sendNotification($type, $app_id, $app_key, $app_icon, $title, $content, $date, $time, $data, $users_id)
    {
        $errors = 0;
        $success = 0;
        $total = 0;
        
        $header = array(
            "en" => $title
        );

        $msg = array(
            "en" => $content
        );
        
        $fields = array(
            'app_id' => $app_id,
            'data' => $data,
            'headings' => $header,
            'contents' => $msg,
            'large_icon' => $app_icon
        );

        #Check if notification will be scheduled
        if ( $date && $time ) {
            $time_zone = $date.' '.$time;
            $time_zone = $this->summer ? $time_zone.' '.'UTC-0500' : $time_zone.' '.'UTC-0600';
            $fields['send_after'] = $time_zone;
        }

        foreach( $users_id as $id ) {
            $total ++;
            $user = User::find( $id );
            if ( $user ) {
                $player_id [] = $user->player_id;
                $fields['include_player_ids'] = $player_id;
                $data = json_encode($fields);
                $res = $this->setNotification($data, $app_key);
                if ( $res['status'] == 'error' ) {#Something went wrong
                    $errors ++;
                } else {
                    $success ++;
                }
            } else {
                \Log::info('Push notification: ID de usuario no encontrado: '.$id);
            }
        }

        if ( $errors > 0 ) {#Some notification was not sended
            $msg_res = 'Se enviaron '.$success.' de '.$total.' notificaciones, faltando '.$errors.' por enviarse';
            \Log::info($msg_res);
            return ['msg' => $msg_res, 'status' => 'error'];
        } else {
            $msg_res = 'Se enviaron '.$success.' de '.$total.' notificaciones';
            \Log::info($msg_res);
            return ['msg' => 'Se enviaron '.$success.' de '.$total.' notificaciones', 'status' => 'success'];
        }
    }

    /**
     * Delete a path/file from server
     *
     */
    public function setNotification($fields, $app_key)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   "Authorization: Basic $app_key"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        
        $res = json_decode($response);

        $str_errors = '';
        if ( property_exists($res, 'errors') ) {
            $msg = 'Notificación no enviada, revise que los parámetros estén escritos correctamente';
            return ['msg' => $msg, 'status' => 'error'];
        } else {
            return ['msg' => 'Notificación enviada exitósamente', 'status' => 'success'];
        }
    }

    /**
     * Delete a path/file from server
     *
     */
    public function delete_path($path)
    {
        if ( $path ) {
            File::delete(public_path( $path ));
            return true;
        }
        return false;
    }

    /*
    * Return boolean, true if mail was sent, false if mail fails
    *
    */
    public function f_mail($params)
    {
        $params['view'] = $params['view'] ? $params['view'] : 'mails.general';
        Mail::send($params['view'], ['content' => $params], function ($message) use($params)
        {
            $message->to($params['email']);
            $message->from(env('MAIL_USERNAME'), env('APP_NAME'));
            $message->subject(env('APP_NAME').' | '.$params['subject']);
        });
        if ( !Mail::failures() ){
            //error_log('enviado');
            return true;
        }
        error_log('error_send: '.Mail::failures());
        return false;
    }

    /**
     * Register a new singin
     *
     */
    public function check_in($user_id)
    {
        $row = New Singin;

        $row->user_id = $user_id;
        $row->date_log = $this->actual_date;
        $row->date_time = $this->actual_datetime;
        
        $row->save();
    }

    /**
     * Verify the status of a cancellation
     *
     */
    public function verify_cancellation(Request $req, Order $order)
    {
        if ( $order->payment_type != "Card" ) { return [ 'status' => 0, 'refund' => 0 ]; }

        $start_datetime = strtotime( $order->created_at );
        $now = strtotime( $this->actual_datetime );
        $dif = $now - $start_datetime;

        if ( $dif < 900 ) { #Before the 15 minutes, refund shipping cost and product cost (Refund 100%)
            return [ 'status' => 1, 'refund' => $order->total_payment ];
        } else if( $dif >= 900 && $dif <= 1800 ) {#Between 15 and 30 minutes, refund only product cost
            return [ 'status' => 2, 'refund' => $order->total_payment - $order->shipping_cost ];
        } else {#After 30 minutes, deny refund
            return [ 'status' => 0, 'refund' => 0 ];
        }
    }

    /*
    * @Param array rows
    *
    */
    public function insertOrUpdate(array $rows, $table) 
    {
        #$table = \DB::getTablePrefix().with(new self)->getTable();

        $first = reset($rows);

        $columns = implode( ',',
            array_map( function( $value ) { return "$value"; } , array_keys($first) )
        );

        $values = implode( ',', array_map( function( $row ) {
                return '('.implode( ',',
                    array_map( function( $value ) { return '"'.str_replace('"', '""', $value).'"'; } , $row )
                ).')';
            } , $rows )
        );

        $updates = implode( ',',
            array_map( function( $value ) { return "$value = VALUES($value)"; } , array_keys($first) )
        );

        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

        return \DB::statement( $sql );
    }

    /**
     * Get the data about articles.
     *
     * @param  json $productos
     */
    public function getArticlesData(array $articles)
    {
        $pro_array = array();
        $invalid_items = array();
        $total = 0;

        foreach ( $articles as $article ) {

            $item = Articulo::where('id', $article['id'])
            ->where('stock', '>=', $article['cantidad'])
            ->first();

            if ( $item ) {#Item exist in database and there is enough stock
                
                $pro_array [] = [ 'id' => $item->id, 'articulo' => $item->nombre, 'precio_u' => ( $item->precio * 100 ), 'cantidad' => $article['cantidad'] ];
                $total += ( $article['precio_u'] * $article['cantidad'] );

            } else {#Product do not exist in db or has not enough stock!

                array_push($invalid_items, ['id' => $article['id'], 'articulo' => $article['articulo'], 'precio_u' => $article['precio_u'], 'cantidad' => 0]);

            }
        }

        return [ 'total' => $total, 'pro_array' => $pro_array, 'invalid_items' => $invalid_items ];
    }

    /**
     * Change the stock of the articles.
     *
     * @param  json $productos
     */
    public function changeArticleStock(array $articles, $action = 'decrement')
    {
        foreach ( $articles as $article ) {
            if ( $action == 'decrement' ) {

                Articulo::where('id', $article['id'])->decrement('stock', $article['cantidad']);
            
            } else {

                Articulo::where('id', $article['articulo_id'])->increment('stock', $article['cantidad']);

            }
        }

        return true;
    }

    /**
     * Change the stock of the products.
     *
     * @param  json $productos
     */
    public function changeProductStock($products, $action = 'decrement')
    {
        foreach ( $products as $product ) {
            if ( $action == 'decrement' ) {

                ProductVariant::where('id', $product->product_variant_id)->decrement('stock', $product->cantidad);
            
            } else {

                ProductVariant::where('id', $product->product_variant_id)->increment('stock', $product->cantidad);

            }
        }

        return true;
    }
}
