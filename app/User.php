<?php

namespace App;

use Conekta\Customer;
use DB;
use Exception;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OpenpayApi;
use OpenpayApiAuthError;
use OpenpayApiConnectionError;
use OpenpayApiError;
use OpenpayApiRequestError;

use Illuminate\Support\Facades\Log;


class User extends Authenticatable
{
  use HasApiTokens, Notifiable;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'users';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'role_id', 'fullname', 'email', 'password', 'photo',
    'phone', 'remember_token', 'social_network', 'player_id', 'status', 'token_payment', 'openpay_id'
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  /**
   * Get the rol of the current user.
   *
   */
  public function role()
  {
    return $this->belongsTo('App\Role');
  }

  /**
   * Get the orders of the current user.
   *
   */
  public function pedidos()
  {
    return $this->hasMany('App\Pedido', 'user_id')->orderBy('fecha', 'desc');
  }

  /**
   * Get the cards of the current user.
   *
   */
  public function tarjetas()
  {
    return $this->hasMany('App\Tarjeta', 'user_id');
  }

  /**
   * Get the addresses of the current user.
   *
   */
  public function direcciones()
  {
    return $this->hasMany('App\Direccion', 'user_id');
  }

  /**
   * Check the role of the current user.
   *
   */
  public function checkRole($roles)
  {
    foreach ($roles as $role) {
      if ($this->role->descripcion == $role) {
        return true;
      }
    }
    return false;
  }

  /**
   * Search an user by his email.
   *
   */
  public static function user_by_email($email, $old_email = false)
  {
    $query = User::where('email', '=', $email);

    $query = $old_email ? $query->where('email', '!=', $old_email)->get() : $query->get();

    return $query;
  }

  /**
   * Search an user by his id.
   */
  public static function user_by_id($id)
  {
    return User::where('id', $id)->first();
  }

  /**
   * Get the users filtered by the given values.
   */
  static function filter_rows($l_usr, $roles = [], $status = null, $verify_player_id = null)
  {
    if ($l_usr->role_id == 1) { #Admin
      $rows = User::query();
    } else { #Any other role wouldn't be able to get any data
      return [];
    }

    if (count($roles)) {
      $rows->whereIn('role_id', $roles);
    }

    if ($status !== null) {
      $rows = $rows->where('status', $status);
    }

    if ($verify_player_id !== null) {
      $rows = $rows->whereNotNull('player_id');
    }

    return $rows->get();
  }

  /**
   * Get the users filtered by the given values.
   */
  static function getTopTen($roles = [], $status = null, $verify_player_id = null)
  {
    $ids = Pedido::select(DB::raw('*, SUM(total) AS "total_paid"'))->groupBy('id_users')->orderBy('total_paid', 'desc')->limit(10)->pluck('id_users');

    $rows = User::whereIn('id', $ids);

    if (count($roles)) {
      $rows->whereIn('role_id', $roles);
    }

    if ($status !== null) {
      $rows = $rows->where('status', $status);
    }

    if ($verify_player_id !== null) {
      $rows = $rows->whereNotNull('player_id');
    }

    return $rows->get();
  }

  public function cards()
  {
    return $this->getCustomerOpenpay()->cards->getList([]);
  }

  public function getCustomerOpenpay()
  {

    $openpay = resolve(OpenpayApi::class);

    if ($this->openpay_id) {

      try {

        $customer = $openpay->customers->get($this->openpay_id);

        return $customer;
      } catch (OpenpayApiRequestError $e) {
        error_log('ERROR on the request: ' . $e->getMessage(), 0);
      } catch (OpenpayApiConnectionError $e) {
        error_log('ERROR while connecting to the API: ' . $e->getMessage(), 0);
      } catch (OpenpayApiAuthError $e) {
        error_log('ERROR on the authentication: ' . $e->getMessage(), 0);
      } catch (OpenpayApiError $e) {
        error_log('ERROR on the API: ' . $e->getMessage(), 0);
      } catch (Exception $e) {
        error_log('Error on the script: ' . $e->getMessage(), 0);
      }
    } else {

      $customerData = array(
        'external_id'      => '1' . $this->id,
        'name'             => $this->fullname,
        'email'            => $this->email,
        'requires_account' => false,
        'phone_number'     => $this->pone,
      );

      $openp_customer = $openpay->customers->add($customerData);

      $this->openpay_id = $openp_customer->id;
      $this->save();

      return $openp_customer;
    }

    return null;
  }
}
