<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $password = Hash::make('mygoodpassword123@');
        User::create([
            'name' => 'Jack',
            'email' => 'jack@test.com',
            'password' => $password,
            'default_currency' => 'USD'
        ]);
    }
}
