<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/test-auth', function(){
    return "OK-AUTH";
});

Route::get('/test/{from}/{to}', function($from, $to){

    $from = strtoupper($from);
    $to = strtoupper($to);

    $response = json_decode('{"success":true,"timestamp":1618782244,"base":"EUR","date":"2021-04-18","rates":{"USD":1.1977,"BRL":6.693633}}');
    $response = (array)$response;
    $rates = (array)$response["rates"];

    $response = Http::get('http://data.fixer.io/api/latest?access_key=782fd0a3c4253af5b9dc67b173a586d5&symbols=USD,BRL&format=1');
    // dd($response->json(), $response->json()["rates"], $response->json()["rates"]["BRL"]);
    $rates = $response->json()["rates"];

    return [
        "from" => $from,
        "to" => $to,
        "original" => 100,
        "converted" => 100 / $rates[$from] * $rates[$to]
    ];
});

Route::post('/register', [RegisterController::class, 'create']);
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/transaction', [TransactionController::class, 'store']);
});
