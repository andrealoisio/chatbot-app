<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'default_currency' => 'required|string|min:3|max:3',
        ]);

        $currencyCode = strtoupper($request->default_currency);

        $transactionController = new TransactionController();

        if (CurrencyController::currency_code_is_invalid($currencyCode)) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'currency_code', 'Invalid currency code'
                );
            });
        }

        $validator->validate();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'default_currency' => $currencyCode,
            'password' => Hash::make($request->password),
        ]);
    }
}
