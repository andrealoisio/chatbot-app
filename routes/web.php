<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::post('/api/login', [LoginController::class, 'authenticate'])->middleware('web');
Route::post('/api/logout', [LoginController::class, 'logout']);

Route::middleware(['auth:sanctum', 'web'])->group(function(){
    Route::post('/api/transaction', [TransactionController::class, 'store']);
    Route::get('/api/account-balance', function(){
        return auth()->user();
    });
});
