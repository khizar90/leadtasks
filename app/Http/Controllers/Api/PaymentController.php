<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function craeteIntent(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $amount  = $request->amount * 100;
        if ($user) {
            $stripeId = null;
            if ($user->stripe_customer_id)
                $stripeId = $user->stripe_customer_id;
            Stripe::setApiKey('sk_test_51PhZizRrSdNfpWAg8uxw0JVSpbkgcvITKcYO8ZRUAuLGiFHOunBsuZ2aApWZEWgd8ZY99MV3FKrfNLZ0Tmvxg3Xo000FZewijA');

            if ($user->stripe_customer_id === '') {

                $person = Customer::create([
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'name' => $user->name,
                    'description' => ''
                ]);
                if (!$stripeId) {

                    $stripeId = $person['id'];
                    User::where('uuid', $user->uuid)->update(['stripe_customer_id' => $stripeId]);
                }
            }
            $user = User::find($request->user()->uuid);
            $intent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'customer' => $user->stripe_customer_id
            ]);

            return response()->json([
                'status' => true,
                'action' =>  'Intent Created',
                'data' => $intent->client_secret
            ]);
        }
    }

    
}
