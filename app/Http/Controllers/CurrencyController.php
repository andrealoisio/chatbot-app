<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CurrencyController extends Controller
{
    public static function get_rates()
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

    public static function convert_amount($amount, $from, $to)
    {
        $rates = CurrencyController::get_rates();
        return $amount / $rates[$from] * $rates[$to];
    }

    public static function currency_code_is_invalid($code)
    {
        $code = strtoupper($code);
        $rates = CurrencyController::get_rates();
        return ($rates[$code] ?? null) == null;
    }

    public function currency_code_list() {
        return array_keys((array) $this::get_rates());
    }
}
