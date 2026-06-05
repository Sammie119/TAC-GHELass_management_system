<?php

namespace App\Services;

use App\Zenoph\Notify\Enums\AuthModel;
use App\Zenoph\Notify\Request\CreditBalanceRequest;
use App\Zenoph\Notify\Request\RequestException;
use App\Zenoph\Notify\Request\SMSRequest;

class SMSOnlineGhService
{
    static function sendSMS($phone, $msg)
    {
        try {
            // Initialise SMS request
            $request = new SMSRequest();

            // set request host and authentication properties
            $request->setHost(config('services.smsonline.base_url'));
            $request->setAuthModel(AuthModel::API_KEY);
            $request->setAuthApiKey(config('services.smsonline.key'));

            // set message properties and submit
            $request->setMessage($msg);
            $request->setSender(config('services.smsonline.sender_id'));
            $request->addDestination($phone);
            $request->submit();

            return true;
        }

        catch (RequestException $ex){
            // get the handshake indicator to know the cause of the error
            $handshake = $ex->getRequestHandshake();
            return false;

            // Exception catch code continues
        }
    }

    public function checkSMSBalance()
    {
        try {
            // create request object
            $request = new CreditBalanceRequest();
            $request->setHost(config('services.smsonline.base_url'));
            $request->useSecureConnection(false);
            $request->setAuthModel(AuthModel::API_KEY);
            $request->setAuthApiKey(config('services.smsonline.key'));

            // get the balance
            $response = $request->submit();

            // output the balance
            $balance = $response->getBalance();

            echo "Your balance is: {$balance}.";
        }

        catch (Exception $ex) {
            die (printf("Error: %s.", $ex->getMessage()));
        }

    }
}
