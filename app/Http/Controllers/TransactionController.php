<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // todo: validate > 0
        // todo: validate type 'WITHDRAW','DEPOSIT'
        $user = auth()->user();
        // dd($user);
        $multiplier =  $request->type == 'DEPOSIT' ? 1 : -1;
        $new_account_balance = $user->account_balance + ($request->value * $multiplier);
        Transaction::create([
            "user_id" => $user->id,
            "type" => $request->type,
            "value" => $request->value,
            "account_balance" => $new_account_balance
        ]);
        $user->account_balance = $new_account_balance;
        $user->save();
    }

    private function get_rates(){
        $rates = Redis::get('rates');
        if ($rates == null) {
            Log::info("Currency conversion cache not found, call the api");
            $response = Http::get(config('currency_api_url').config('app.currency_api_key'));
            $rates = $response->json()["rates"];
            Redis::set('rates', json_encode($rates));
            Redis::expire('rates', config('app.rates_expiration_time'));
        } else {
            $rates = (array)json_decode($rates);
            Log::info("Currency convertion cache found");
        }
        return $rates;
    }

    private function get_currency_list() {
        $rates = $this->get_rates();
        dd($rates);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
