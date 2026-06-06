<?php

namespace App\Http\Controllers;

use App\Models\IncomeRecord;
use App\Models\Member;
use App\Models\OnlinePayment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class GuestPaymentController extends Controller
{
    public function show()
    {
        return view('give.show');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone'     => 'required|string|max:20',
            'email'     => 'nullable|email|max:150',
            'category'  => 'required|string',
            'amount'    => 'required|numeric|min:1',
            'notes'     => 'nullable|string|max:255',
        ]);

        // Store as online payment without member_id
        // We store guest details in notes
        OnlinePayment::create([
            'member_id' => null,
            'category'  => $validated['category'],
            'amount'    => $validated['amount'],
            'currency'  => 'GHS',
            'phone'     => $validated['phone'],
            'reference' => $validated['email'],
            'provider'  => 'momo',
            'status'    => 'pending',
            'notes'     => "Guest: {$validated['full_name']} | Email: " .
                ($validated['email'] ?? 'N/A') .
                " | Notes: " . ($validated['notes'] ?? 'N/A'),
        ]);

        if ($validated['category'] === 'project')
            PaymentService::makePayment($validated['email'], $validated['amount'], config('services.paystack.callback_url_guest'),'Project');
        else
            PaymentService::makePayment($validated['email'], $validated['amount'], config('services.paystack.callback_url_guest'),'Main');
    }

    public function thanks(Request $request)
    {
        $guest_payment = 0;
        $name = '';
        if(isset($request['reference'])){
            $response = PaymentService::verifyTransaction($request['reference']);

//            dd($response);
            if ($response['status'] && $response['data']['status'] === 'success') {

                $payment = OnlinePayment::where('reference', $response['data']['customer']['email'])->orderByDesc('id')->first();

                if(!$payment){
                    $payment = OnlinePayment::where('reference', $response['data']['reference'])->orderByDesc('id')->first();
                }

                $name = trim(
                    explode('|', str_replace('Guest:', '', $payment->notes))[0]
                );

                if($payment){
                    $payment->update([
                        'reference' => $response['data']['reference'],
                        'provider'  => $response['data']['channel'],
                        'status'    => 'confirmed',
                        'confirmed_at' => now(),
                        'confirmed_by' => 1,
                    ]);

                    // Create income record
                    $guest_payment = IncomeRecord::firstOrCreate([
                        'reference'      => $response['data']['reference'],
                        'amount'         => $payment->amount,
                        ],
                        [
                        'member_id'      => null,
                        'category'       => $payment->category,
                        'currency'       => $payment->currency,
                        'amount_ghs'     => $payment->amount,
                        'exchange_rate'  => 1,
                        'payment_date'   => today(),
                        'payment_method' => 'online',
                        'notes'          => $payment->notes,
                        'status'         => 'confirmed',
                        'recorded_by'    => 1,
                    ]);
                }
            }

            return view('give.thanks', [
                'ref'      => $response['data']['reference'],
                'name'     => $name,
                'amount'   => $guest_payment->amount,
                'currency' => $guest_payment->currency,
                'category' => $guest_payment->category,
            ]);
        } else {
            return 0;
        }
    }
}
