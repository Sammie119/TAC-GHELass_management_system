<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;

class PaymentService
{
    public static function makePayment($email, $amount, $callback_url, $acc_type = 'Main')
    {
//        $amount = ceil(Utils::exchange_rate($amount) * 100);
        $amount = ceil(($amount + ($amount * 0.01)) * 100);

        $url = config('services.paystack.payment_url');

        $fields_main = [
            'email' => $email,
            'amount' => $amount,
            'subaccount' => "ACCT_ofarpr9h8tooynl",
            'callback_url' => $callback_url,
        ];

        $fields_project = [
            'email' => $email,
            'amount' => $amount,
            'subaccount' => "ACCT_kx456d8hruovser",
            'callback_url' => $callback_url,
        ];

        if($acc_type === 'Main'){
            $fields_string = http_build_query($fields_main);
        } else {
            $fields_string = http_build_query($fields_project);
        }


        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ". config('services.paystack.secret_key'),
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($result, true);

        if (!$response || !$response['status']) {
            die('Paystack Error: ' . ($response['message'] ?? 'Unknown error'));
        }

        header("Location: " . $response['data']['authorization_url']);
        exit;
    }

    public static function verifyTransaction($reference)
    {
        try {
            $client = new Client([
                'base_uri' => config('services.paystack.payment_url'),
                'headers' => [
                    'Authorization' => 'Bearer '.config('services.paystack.secret_key'),
                    'Content-Type' => 'application/json',
                ],
                'verify' => !App::isLocal(), // ← Add this line to disable SSL verification
            ]);

            $response = $client->get("/transaction/verify/{$reference}");

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $error = json_decode(
                $e->getResponse()->getBody()->getContents(),
                true
            );

            return [
                'status' => false,
                'message' => 'Unable to verify transaction',
                'error' => $error['message'] ?? 'Verification failed'
            ];

        }
    }
}
