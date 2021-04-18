<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'default_currency' => 'required|string|min:3|max:3',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'default_currency' => $request->default_currency,
            'password' => Hash::make($request->password),
        ]);
    }
}
