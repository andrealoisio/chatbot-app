<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AccountBalanceTest extends DuskTestCase
{
    /**
     * Verify account balance process
     *
     * @return void
     */
    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe@test.com')->delete();
            $user = User::factory()->create([
                'email' => 'john-doe@test.com',
                'default_currency' => 'USD'
            ]);
            $browser
                ->visit('/')
                ->type('#entry', 'i want to login')
                ->press('#send')
                ->type('#entry', $user->email)
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Login success!')
                ->assertSee('Login success!')
                ->type('#entry', 'may i see my account balance')
                ->press('#send')
                ->waitForText('Your account balance is 0.00 USD')
                ->assertSee('Your account balance is 0.00 USD')
                ->type('#entry', 'i want to deposit 155.32 usd')
                ->press('#send')
                ->waitForText('Your account balance is 155.32 USD')
                ->assertSee('Your account balance is 155.32 USD')
            ;
        });
    }
}
