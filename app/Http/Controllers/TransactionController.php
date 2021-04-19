<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

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

        if (isset($request->currency_code) && $this->currency_code_is_invalid($request->currency_code)) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'currency_code', 'Invalid currency code'
                );
            });
        }

        $user = auth()->user();

        $amount = $request->amount;
        if (isset($request->currency_code) && strtoupper($request->currency_code) != strtoupper($user->default_currency)) {
            $amount = $this->convert_amount($amount, strtoupper($request->currency_code), strtoupper($user->default_currency));
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

    private function get_rates()
    {
        $rates = Redis::get('rates');
        if ($rates == null) {
            Log::info("Currency conversion cache not found, call the api");
            $response = Http::get(config('app.currency_api_url') . config('app.currency_api_key'));
            $rates = $response->json()["rates"];
            Redis::set('rates', json_encode($rates));
            Redis::expire('rates', config('app.rates_expiration_time'));
        } else {
            $rates = (array)json_decode($rates);
            Log::info("Currency convertion cache found");
        }
        return $rates;
    }

    function currency_code_is_invalid($code)
    {
        $code = strtoupper($code);
        $rates = $this->get_rates();
        return ($rates[$code] ?? null) == null;
    }

    private function convert_amount($amount, $from, $to)
    {
        $rates = $this->get_rates();
        return $amount / $rates[$from] * $rates[$to];
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
