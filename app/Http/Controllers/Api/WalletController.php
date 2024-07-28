<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\WalletRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Stripe\Account;
use Stripe\Stripe;
use Stripe\StripeClient;

class WalletController extends Controller
{
    public function create(Request $request)
    {

        $user = User::find($request->user()->uuid);
        if ($user) {
            if ($user->stripe_connect_id === '') {
                Stripe::setApiKey('sk_test_51PhZizRrSdNfpWAg8uxw0JVSpbkgcvITKcYO8ZRUAuLGiFHOunBsuZ2aApWZEWgd8ZY99MV3FKrfNLZ0Tmvxg3Xo000FZewijA');
                $createConnectedAccount = Account::create([
                    'type' => 'custom',
                    'country' => 'US',
                    'email' => $request->email,
                    'business_type' => 'company',
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                    'business_profile' => [
                        'mcc' => '5734',
                        'name' => $request->business_name,
                        'product_description' => 'ConnectApp User for Freelancer',
                        'support_phone' => $request->business_number,
                        'support_email' => $request->business_email,
                        'support_address' => [
                            'city' => $request->business_city,
                            'state' => $request->business_state,
                            'postal_code' => $request->business_postal_code,
                            'line1' => $request->business_address,
                        ]
                    ],
                    'company' => [
                        'name' => $request->business_name,
                        'structure' => 'single_member_llc',
                        'phone' => $request->business_number,
                        'address' => [
                            'city' => $request->business_city,
                            'state' => $request->business_state,
                            'postal_code' => $request->business_postal_code,
                            'line1' => $request->business_address,
                        ]
                    ],
                    'settings' => [
                        'payments' => [
                            'statement_descriptor' => $request->business_name
                        ]
                    ],
                    'tos_acceptance' => [
                        'date' => time(),
                        'ip' => $request->ip(),
                    ]
                ]);
                Account::createPerson($createConnectedAccount->id, [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->number,
                    'ssn_last_4' => $request->ssn_last4,
                    'id_number' => $request->tax_id,
                    'dob' => [
                        'year' => explode('-', $request->dob)[0],
                        'month' => explode('-', $request->dob)[1],
                        'day' => explode('-', $request->dob)[2],
                    ], 'address' => [
                        'city' => $request->city,
                        'state' => $request->state,
                        'postal_code' => $request->postal_code,
                        'line1' => $request->address,
                    ],
                    'relationship' => [
                        'representative' => true,
                        'owner' => true,
                        'executive' => true
                    ]
                ]);
                Account::createExternalAccount($createConnectedAccount->id, [
                    'external_account' => [
                        'object' => 'bank_account',
                        'country' => 'US',
                        'currency' => 'USD',
                        'account_holder_name' => $request->account_holder_name,
                        'account_number' => $request->account_no,
                        'routing_number' => $request->account_routing_no,
                    ]
                ]);
                User::where('uuid', $request->user()->uuid)->update(['stripe_connect_id' => $createConnectedAccount->id]);
            }
            $getUser = User::find($request->user()->uuid);
            return response()->json(['status' => true, 'data' => $getUser, 'action' => 'Wallet created']);
        } else
            return response()->json(['status' => false, 'data' => new stdClass(), 'action' => 'Wallet user not found']);
    }

    public function detail($id)
    {
        $wallet = User::where('uuid', $id)->first();
        if ($wallet) {
            if ($wallet->stripe_connect_id != '') {
                $wallet->requirements = WalletRequirement::where('user_id', $wallet->id)->latest()->get();
                $payout = number_format(Payment::where('user_id', $id)->where('type', 'payout')->sum('price'), 2, '.', '');
                $wallet->payout = $payout;
                $income = Payment::where('user_id', $id)->where('type', '!=', 'refund')->sum('price');
                $wallet->income = number_format($income, 2, '.', '');
                $wallet->balance = number_format(($income) - $payout, 2, '.', '');
                $wallet->payments = Payment::where('user_id', $id)->latest()->Paginate(25);
                return response()->json(['status' => true, 'data' => $wallet, 'action' => 'Wallet']);
            } else
                return response()->json(['status' => false, 'data' => new stdClass(), 'action' => 'Wallet not created yet']);
        } else
            return response()->json(['status' => false, 'data' => new stdClass(), 'action' => 'Wallet user not found']);
    }

    public function payout(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        return response()->json([
            'status' => true,
            'action' => "Payout Successfully",
        ]);
    }
}
