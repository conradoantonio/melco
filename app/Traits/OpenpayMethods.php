<?php

namespace App\Traits;

use Openpay;
use OpenpayApiError;
use OpenpayApiRequestError;
use OpenpayApiResourceBase;
use OpenpayApiTransactionError;

use \App\User;
use \App\Card;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait OpenpayMethods
{
    /**
     * Register a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function saveOpenpayCustomer(Request $req)
    {
        try {
            $this->openpay = Openpay::getInstance();
            $customer = $this->openpay->customers->add(
                array(
                    'name' => $req->fullname,
                    'email' => $req->email,
                    'phone_number' => '1122334455',
                )
            );

            return ['msg' => 'Cliente registado correctamente en openpay', 'status' => 'success', 'data' => $customer];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR on the transaction: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en el request: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al registrar el usuario en openpay: '. $e->getMessage(), 0);
            return ['msg' => 'Error al registrar el usuario, trate usando otros datos', 'status' => 'error'];
        }
    }

    /**
     * Add a card for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function addCard($card_data)
    {
        Log::info('user => ' . $this->current_user);

        $customer = $this->openpay->customers->get($this->current_user->openpay_id);

        $cardData = array(
            'holder_name' => 'Teofilo Velazco',
            'card_number' => '4916394462033681',
            'cvv2' => '123',
            'expiration_month' => '12',
            'expiration_year' => '15',
            'address' => array(
                    'line1' => 'Privada Rio No. 12',
                    'line2' => 'Co. El Tintero',
                    'line3' => '',
                    'postal_code' => '76920',
                    'state' => 'Querétaro',
                    'city' => 'Querétaro.',
                    'country_code' => 'MX'));

        $card = $customer->cards->add($cardData);

    }

    /**
     * Register a card for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function saveOpenpayCard(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return ['msg' => 'ID de usuario no encontrado', 'status' => 'error']; }

        $this->openpay = Openpay::getInstance();
        $customer = $this->openpay->customers->get($user->openpay_id);

        if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos en líneo', 'status' => 'error']; }

        try {

            $cardDataRequest = array(
                'token_id' => $req->card_token,
                'device_session_id' => $req->device_session_id
            );

            $card = $customer->cards->add($cardDataRequest);

            /*$card = $customer->cards->add(
                array(
                    'token_id' => $req->card_token,
                    'device_session_id' => $req->device_session_id
                )
            );*/
            /*$cardDataRequest = array(
                'holder_name' => $req->tarjetahabiente,
                'card_number' => $req->numero,
                'cvv2' => $req->cvv,
                'expiration_month' => $req->mes,
                'expiration_year' => $req->anio,
                'device_session_id' => $req->device_session_id,
                /*'address' => array(
                    'line1' => $req->line1,
                    'postal_code' => $req->postal_code,
                    'state' => $req->state,
                    'city' => $req->city,
                    'country_code' => 'MX'
                )
            );

            $card = $customer->cards->add($cardDataRequest);*/

            return ['msg' => 'Tarjeta guardada correctamente', 'status' => 'success', 'data' => $card];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR on the transaction: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en el request: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al registrar el usuario en openpay: '. $e->getMessage(), 0);
            return ['msg' => 'Error al registrar el usuario, trate usando otros datos', 'status' => 'error'];
        }
    }

    /**
     * Delete a card for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteOpenpayCard(Request $req, $card_token)
    {
        $this->openpay = Openpay::getInstance();

        $user = User::find($req->user_id);
        if (! $user ) { return ['msg' => 'ID de usuario no encontrado', 'status' => 'error']; }

        $customer = $this->openpay->customers->get($user->openpay_id);

        if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos en línea', 'status' => 'error']; }

        try {

            $item = $customer->cards->get($card_token);
            $item->delete();

            return ['msg' => 'Tarjeta eliminada correctamente', 'status' => 'success', 'data' => $item];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR en la transacción: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en la petición: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al tratar de eliminar la tarjeta: '. $e->getMessage(), 0);
            return ['msg' => 'Error al eliminar la tarjeta', 'status' => 'error'];
        }
    }

    /**
     * Delete a card for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function suscribeCustomer(Request $req, User $user, TipoMembresia $plan, Tarjeta $card)
    {
        try {
            $subscriptionDataRequest = array(
                'plan_id' => $plan->id_plan_openpay,
                'source_id' => $card->token
            );

            $this->openpay = Openpay::getInstance();
            $customer = $this->openpay->customers->get($user->openpay_id);

            if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos en líneo', 'status' => 'error']; }

            $subscription = $customer->subscriptions->add($subscriptionDataRequest);

            return ['msg' => 'Suscripción adquirida correctamente', 'status' => 'success', 'data' => $subscription];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR en la transacción: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en la petición: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al tratar de eliminar la tarjeta: '. $e->getMessage(), 0);
            return ['msg' => 'Error al eliminar la tarjeta', 'status' => 'error'];
        }
    }

    /**
     * Cancell a suscription for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function cancellSuscription(Request $req, User $user, MembresiaUsuario $membresia)
    {
        try {
            $this->openpay = Openpay::getInstance();

            $customer = $this->openpay->customers->get($user->openpay_id);

            if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos online', 'status' => 'error']; }

            $subscription = $customer->subscriptions->get($membresia->id_suscripcion);
            $subscription->delete();

            return ['msg' => 'Suscripción cancelada correctamente', 'status' => 'success', 'data' => $subscription];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR en la transacción: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en la petición: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al tratar de cancelar la suscripción: '. $e->getMessage(), 0);
            return ['msg' => 'Error al cancelar la suscripción', 'status' => 'error'];
        }
    }

    /**
     * Create a charge for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function generateSpeiPayment(User $user, $cost, $order_id, Request $req)
    {
        try {
            $this->openpay = Openpay::getInstance();
            $customer = $this->openpay->customers->get($user->openpay_id);

            if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos online', 'status' => 'error']; }

            $chargeRequest = array(
                'method' => 'bank_account',
                'amount' => round($cost, 2, PHP_ROUND_HALF_UP),
                'description' => 'Cargo de órden para artículos',
                'order_id' => 'oid-'.$order_id
            );

            $order = $customer->charges->create($chargeRequest);

            return ['msg' => 'Monto pagado correctamente', 'status' => 'success', 'data' => $order];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR en la transacción: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en la petición: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al tratar de pagar el servicio: '. $e->getMessage(), 0);
            return ['msg' => 'Error al pagar el servicio', 'status' => 'error'];
        }
    }


    /**
     * Create a charge for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function payOrder(User $user, $cost, /*Tarjeta*/ $card, Request $req)
    {
        try {
            $this->openpay = Openpay::getInstance();
            $customer = $this->openpay->customers->get($user->openpay_id);

            if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos online', 'status' => 'error']; }

            $chargeRequest = array(
                'method' => 'card',
                #'source_id' => 'kvkfbk9u5ztijbtuqtxq',
                // 'source_id' => $card->token,
                'source_id' => $card->token,
                'amount' => round($cost, 2, PHP_ROUND_HALF_UP),
                'currency' => 'MXN',
                'description' => 'Cargo por compra de cargamento de material vegetal',
                #'order_id' => 'oid-00051',
                'device_session_id' => 'kR1MiQhz2otdIuUlQkbEyitIqVMiI16f',
                #'device_session_id' => $req->device_session_id,
            );

            $order = $customer->charges->create($chargeRequest);

            return ['msg' => 'Monto pagado correctamente', 'status' => 'success', 'data' => $order];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR en la transacción: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en la petición: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error al conectar a la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al tratar de pagar el servicio: '. $e->getMessage(), 0);
            return ['msg' => 'Error al pagar el servicio', 'status' => 'error'];
        }
    }

    /**
     * Create a refund for an order on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function refund(Request $req, User $user, $order, $refund_amount)
    {
        try {
            $this->openpay = Openpay::getInstance();
            $customer = $this->openpay->customers->get($user->openpay_id);

            if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos online', 'status' => 'error']; }

            $refundData = array(
                'description' => $req->comment,
                'amount' => $refund_amount
            );
            $charge = $customer->charges->get($order->token_orden);
            $charge->refund($refundData);

            return ['msg' => 'Servicio reembolsado correctamente', 'status' => 'success'];
        } catch (\OpenpayApiTransactionError $e) {
            error_log('ERROR en la transacción: ' . $e->getMessage() .
            ' [error code: ' . $e->getErrorCode() .
            ', error category: ' . $e->getCategory() .
            ', HTTP code: '. $e->getHttpCode() .
            ', request ID: ' . $e->getRequestId() . ']', 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiRequestError $e) {
            error_log('Error en la petición: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiConnectionError $e) {
            error_log('Error con openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiAuthError $e) {
            error_log('Error de autenticación: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\OpenpayApiError $e) {
            error_log('Error en la api de openpay: ' . $e->getMessage(), 0);
            return ['msg' => $e->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            error_log('Error al tratar de reembolsar el servicio: '. $e->getMessage(), 0);
            return ['msg' => 'Error al reembolsar el servicio', 'status' => 'error'];
        }
    }
}
