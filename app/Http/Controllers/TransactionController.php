<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'type' => 'required|in:DEPOSIT,WITHDRAW'
        ]);

        if (isset($request->currency_code) && CurrencyController::currency_code_is_invalid($request->currency_code)) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'currency_code', 'Invalid currency code'
                );
            });
        }

        $user = auth()->user();

        $amount = $request->amount;
        if (isset($request->currency_code) && strtoupper($request->currency_code) != strtoupper($user->default_currency)) {
            $amount = CurrencyController::convert_amount($amount, strtoupper($request->currency_code), strtoupper($user->default_currency));
        }

        if ($request->type == 'WITHDRAW' && $amount > $user->account_balance) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'amount', 'Insuficient funds'
                );
            });
        }

        $validator->validate();

        $multiplier = $request->type == 'DEPOSIT' ? 1 : -1;
        $new_account_balance = $user->account_balance + ($amount * $multiplier);

        Transaction::create([
            "user_id" => $user->id,
            "type" => $request->type,
            "value" => $amount,
            "account_balance" => $new_account_balance
        ]);

        $user->account_balance = $new_account_balance;
        $user->save();
    }
}
