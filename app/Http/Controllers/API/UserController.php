<?php

namespace App\Http\Controllers\API;

use DB;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Conekta\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

use Validator;

use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
  public $successStatus = 200;

  /**
   * Sign up a new customer (Login)
   *
   * @param  Request $req
   * @return $customer
   */
  public function signUpCustomer(Request $req)
  {
    //If customer already exist check if is registered with some social network
    if (count(User::user_by_email($req->email))) {
      //If its a social network, means that user does not need to registered.
      if ($req->social_network) {
        $customer = User::where('email', $req->email)
          ->where('social_network', '!=', 0) //Facebook or google
          ->where('role_id', 3) //Customer
          ->first();

        if ($customer) {
          $customer->role;
          //$this->check_in($customer->id);
          return response([
            'msg'    => 'Usuario logueado correctamente',
            'status' => 'success',
            'data'   => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at']),
            'token'  => $customer->createToken('melwinApp')->accessToken,
          ], 200);
        }
        return response(['msg' => 'Inicio de sesión inválido, verifique sus datos porfavor', 'status' => 'error'], 200);
      }
      return response(['msg' => 'Éste correo ya está siendo utilizado, porfavor, elija uno diferente', 'status' => 'error'], 200);
    } else {

      $customer = new User;

      if (!$req->social_network) {
        $customer->password = bcrypt($req->password);
      }
      $customer->fullname = $req->fullname;
      $customer->email = $req->email;
      $customer->photo = 'img/users/default.jpg';
      $customer->social_network = $req->social_network;
      $customer->player_id = $req->player_id;
      $customer->role_id = 3; //Role customer

      $customer->save();

      $customer->role;

      return response(['msg' => 'Usuario registrado correctamente', 'status' => 'success', 'data' => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at'])], 200);
    }
  }

  /**
   * Customer login
   *
   * @param  Request  $request
   * @return response json if credentials are correct and status is active (1)
   */
  public function signInCustomer(Request $req)
  {
    $data = $req->json()->all();

    $customer = User::where('email', $data['username'])
      ->where('status', 1)
      ->whereNotIn('role_id', [1, 4])
      ->first();
    if ($customer) {
      if (Hash::check($data['password'], $customer->password)) {
        $customer->role;
        $customer->state;

        Artisan::call('passport:install');

        return response([
          'msg'    => 'Inicio de sesión correcto',
          'status' => 'Active',
          'sessionToken'  => $customer->createToken('melwinApp')->accessToken,
          'name'     => $customer->fullname,
          'email'    => $customer->email,
          'username' => $customer->email,
          'photo'    => "{$req->getSchemeAndHttpHost()}/{$customer->photo}",
          'objectId' => $customer->id,
          'data'     => $customer->setHidden(['role_id', 'password', 'remember_token', 'updated_at'])
        ], 200);
      }
      return response(['error' => 'Contraseña errónea', 'status' => 'error', 'code' => 101], 404);
    }
    return response(['error' => 'Correo inválido', 'status' => 'error', 'code' => 101], 404);
  }

  public function update(Request $req)
  {
    Log::info('on update');

    $data = $req->json()->all();

    $customer = User::where('email', $data['username'])
      ->where('status', 1)
      ->whereNotIn('role_id', [1, 4])
      ->first();
    if ($customer) {
      $customer->fullname = $data['name'];
      $customer->email = $data['username'];

      $customer->save();

      return response(['msg' => 'success', 'status' => 'success'], 200);
    }
  }

  public function photo(Request $req)
  {
    $user = User::find($req->id);

    if ($req->isMethod('get')) {

      return response()->json(['msg' => 'success', 'filename' => $user->photo]);
    } else {

      $validator = Validator::make(
        $req->all(),
        [
          'file' => 'image',
        ],
        [
          'file.image' => 'The file must be an image (jpeg, png, bmp, gif, or svg)'
        ]
      );
      if ($validator->fails())
        return array(
          'fail' => true,
          'errors' => $validator->errors(),
          'file' =>  $req->file('file'),
        );

      $extension = $req->file('file')->getClientOriginalExtension();
      $dir = 'uploads/user/' . $user->id;
      $filename = 'photo-' . uniqid() . '.' . $extension;
      $req->file('file')->move($dir, $filename);

      Log::info("photo 0= > " . public_path($user->photo));

      \File::Delete(public_path($user->photo));

      $user->photo = "{$dir}/{$filename}";

      $user->save();

      return response()->json(['msg' => 'success', 'filename' => "{$req->getSchemeAndHttpHost()}/{$user->photo}"]);
    }
  }

  /**
   * Send an email with a new password
   *
   * @return view mail
   */
  public function recoverPassword(Request $req)
  {
    $item = User::where(['email' => $req->email])->first();

    if (!$item) {
      return response(['msg' => "Este correo no pertenece a ninguna cuenta asociada", 'status' => 'error'], 200);
    }

    if ($item->role_id != 2 && $item->role_id != 3) {
      return response(['msg' => "No se puede restablecer la contraseña, lo sentimos", 'status' => 'error'], 200);
    }

    $pass = str_random(6);

    $item->password = bcrypt($pass);

    if ($item) {
      $newPass = str_random(8);

      $item->password = bcrypt($newPass);
      $item->save();

      $params = array();

      $params['view'] = 'mails.reset_password';
      $params['subject'] = 'Cambio de contraseña';
      $params['user'] = $item;
      $params['email'] = $item->email;
      $params['password'] = $newPass;

      $this->f_mail($params);

      return response(['msg' => 'Correo enviado exitósamente', 'status' => 'success'], 200);
    }

    return response(['msg' => 'Ocurrió un error tratando de enviar el correo, trate nuevamente', 'status' => 'error'], 200);
  }

  public function card(Request $req)
  {

    $data = $req->json()->all();

    $customer_id = $data['where']['user']['objectId'] ?? $data['user']['objectId'];

    $customer = User::find($customer_id);

    $cards = $customer->cards();

    $items = [];

    foreach ($cards as $card) {
      $items[] = $this->arrayCardItem($card);
    }

    if (isset($data['addCard'])) {
      $cardDataRequest = array(
        'token_id' => $data['addCard']['openpayToken'],
        'device_session_id' => $data['addCard']['deviceDataId'],
      );

      $openpCustomer = $customer->getCustomerOpenpay();

      $card = $openpCustomer->cards->add($cardDataRequest);

      return response($this->arrayCardItem($card));
    }


    return response(['results' => $items, 'status' => 'success'], 200);
  }

  public function cardDestroy(Request $req, $id){
    $data = $req->json()->all();

    if(isset($data['deleteToken'])){

      $data_user = array_keys($data)[1];

      $user_id = substr($data_user, strpos($data_user, "-") + 1);

      $customer = User::find($user_id);

      $customer->getCustomerOpenpay()->cards->get($id)->delete();
    }

    return response([], 200);
  }

  public function arrayCardItem($card){
    return [
      'objectId' => $card->id,
      'brand'    => $card->brand,
      'last4'    => $card->card_number,
      'expMonth' => $card->expiration_month,
      'expYear'  => substr($card->expiration_year, -2),
      'cardId'   => $card->id
    ];

  }
}
