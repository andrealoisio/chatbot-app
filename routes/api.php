<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

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

    // Redis::del('rates');

    $rates = Redis::get('rates');

    if ($rates == null) {
        Log::info("Currency conversion cache not found, call the api");
        $response = Http::get('http://data.fixer.io/api/latest?access_key=782fd0a3c4253af5b9dc67b173a586d5&format=1');
        $rates = $response->json()["rates"];

//        $response = json_decode('{"success":true,"timestamp":1618782244,"base":"EUR","date":"2021-04-18","rates":{"AED":4.399395,"AFN":92.889222,"ALL":123.118969,"AMD":624.844766,"ANG":2.151982,"AOA":779.596074,"ARS":111.165697,"AUD":1.55007,"AWG":2.15586,"AZN":2.03085,"BAM":1.955814,"BBD":2.42058,"BDT":101.597849,"BGN":1.955784,"BHD":0.451996,"BIF":2334.894941,"BMD":1.1977,"BND":1.59764,"BOB":8.284131,"BRL":6.693633,"BSD":1.19889,"BTC":0.000021410532,"BTN":89.017554,"BWP":12.974791,"BYN":3.113074,"BYR":23474.919347,"BZD":2.41658,"CAD":1.498317,"CDF":2390.608974,"CHF":1.101435,"CLF":0.030506,"CLP":841.740366,"CNY":7.810187,"COP":4330.263725,"CRC":735.243841,"CUC":1.1977,"CUP":31.739049,"CVE":110.264076,"CZK":25.93272,"DJF":213.428212,"DKK":7.436282,"DOP":68.167429,"DZD":158.452673,"EGP":18.791999,"ERN":17.967788,"ETB":50.08298,"EUR":1,"FJD":2.427017,"FKP":0.869979,"GBP":0.865949,"GEL":4.10209,"GGP":0.869979,"GHS":6.923442,"GIP":0.869979,"GMD":61.178558,"GNF":11990.943903,"GTQ":9.252222,"GYD":250.817899,"HKD":9.307075,"HNL":28.824759,"HRK":7.565152,"HTG":99.303956,"HUF":360.951438,"IDR":17398.867646,"ILS":3.926887,"IMP":0.869979,"INR":89.277155,"IQD":1749.145347,"IRR":50429.157403,"ISK":151.425785,"JEP":0.869979,"JMD":179.829494,"JOD":0.849772,"JPY":130.293586,"KES":128.87802,"KGS":101.570224,"KHR":4850.994987,"KMF":492.074544,"KPW":1077.9302,"KRW":1337.411236,"KWD":0.361047,"KYD":0.999092,"KZT":515.815679,"LAK":11299.305344,"LBP":1812.884813,"LKR":231.380962,"LRD":206.63321,"LSL":17.175263,"LTL":3.536496,"LVL":0.724477,"LYD":5.395255,"MAD":10.70711,"MDL":21.49912,"MGA":4553.421855,"MKD":61.614484,"MMK":1690.399839,"MNT":3414.226265,"MOP":9.59362,"MRO":427.578682,"MUR":48.374195,"MVR":18.504367,"MWK":943.759094,"MXN":23.868131,"MYR":4.941762,"MZN":66.490248,"NAD":17.174528,"NGN":455.73132,"NIO":41.823713,"NOK":10.032216,"NPR":142.427807,"NZD":1.67885,"OMR":0.461426,"PAB":1.19889,"PEN":4.347764,"PGK":4.263364,"PHP":57.923515,"PKR":183.726061,"PLN":4.544852,"PYG":7715.535366,"QAR":4.360526,"RON":4.923697,"RSD":117.499016,"RUB":90.55678,"RWF":1199.299953,"SAR":4.492097,"SBD":9.577807,"SCR":16.731831,"SDG":456.915548,"SEK":10.116241,"SGD":1.598289,"SHP":0.869979,"SLL":12246.48256,"SOS":699.456504,"SRD":16.952264,"STD":24827.287929,"SVC":10.489912,"SYP":1506.188444,"SZL":17.080257,"THB":37.406531,"TJS":13.669485,"TMT":4.203927,"TND":3.301461,"TOP":2.715006,"TRY":9.670264,"TTD":8.137432,"TWD":33.854783,"TZS":2780.16671,"UAH":33.555619,"UGX":4336.263674,"USD":1.1977,"UYU":52.911557,"UZS":12602.684425,"VEF":256104450170.96948,"VND":27644.573946,"VUV":131.194693,"WST":3.032249,"XAF":655.951505,"XAG":0.046163,"XAU":0.000674,"XCD":3.236844,"XDR":0.839193,"XOF":655.951505,"XPF":119.829951,"YER":299.905152,"ZAR":17.155579,"ZMK":10780.733699,"ZMW":26.602777,"ZWL":385.659623}}');
//        $response = (array)$response;
//        $rates = (array)$response["rates"];

        Redis::set('rates', json_encode($rates));
        Redis::expire('rates', config('app.rates_expiration_time'));
    } else {
        $rates = (array)json_decode($rates);
        Log::info("Currency convertion cache found");
    }

    // dd($rates);

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
