<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TransactionTest extends DuskTestCase
{

    /**
     * Verify deposit process
     *
     * @return void
     */
    public function testDepositNoValue()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe@test.com')->delete();
            $user = User::factory()->create([
                'email' => 'john-doe@test.com',
                'default_currency' => 'USD'
            ]);
            $browser
                ->visit('/')
                ->type('#entry', 'login')
                ->press('#send')
                ->type('#entry', $user->email)
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Login success!')
                ->type('#entry', 'deposit')
                ->press('#send')
                ->type('#entry', '100')
                ->press('#send')
                ->waitForText('Your account balance is 100.00 USD')
                ->assertSee('Your account balance is 100.00 USD')
            ;
        });
    }

    /**
     * Verify deposit process
     *
     * @return void
     */
    public function testDepositValue()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe@test.com')->delete();
            $user = User::factory()->create([
                'email' => 'john-doe@test.com',
                'default_currency' => 'USD'
            ]);
            $browser
                ->visit('/')
                ->type('#entry', 'login')
                ->press('#send')
                ->type('#entry', $user->email)
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Login success!')
                ->type('#entry', 'i want to deposit 100')
                ->press('#send')
                ->waitForText('Your account balance is 100.00 USD')
                ->assertSee('Your account balance is 100.00 USD')
            ;
        });
    }

    /**
     * Verify deposit process with different currency code
     *
     * @return void
     */
    public function testDepositValueAnotherCurrency()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe@test.com')->delete();
            $user = User::factory()->create([
                'email' => 'john-doe@test.com',
                'default_currency' => 'USD'
            ]);
            $browser
                ->visit('/')
                ->type('#entry', 'login')
                ->press('#send')
                ->type('#entry', $user->email)
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Login success!')
                ->type('#entry', 'i want to deposit 100 brl')
                ->press('#send')
                ->waitForText('Your account balance is')
                ->assertDontSee('Your account balance is 100.00 USD')
            ;
        });
    }
    /**
     * Verify deposit process
     *
     * @return void
     */
    public function testWithDrawNoFunds()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe@test.com')->delete();
            $user = User::factory()->create([
                'email' => 'john-doe@test.com',
                'default_currency' => 'USD'
            ]);
            $browser
                ->visit('/')
                ->type('#entry', 'login')
                ->press('#send')
                ->type('#entry', $user->email)
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Login success!')
                ->type('#entry', 'i want to withdraw 100')
                ->press('#send')
                ->waitForText('The given data was invalid')
                ->assertSee('Insuficient funds')
            ;
        });
    }
    /**
     * Verify deposit process
     *
     * @return void
     */
    /**
     * Verify deposit process
     *
     * @return void
     */
    public function testWithDraw()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe@test.com')->delete();
            $user = User::factory()->create([
                'email' => 'john-doe@test.com',
                'default_currency' => 'USD'
            ]);
            $browser
                ->visit('/')
                ->type('#entry', 'login')
                ->press('#send')
                ->type('#entry', $user->email)
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Login success!')
                ->type('#entry', 'i want to deposit 100')
                ->press('#send')
                ->waitForText('Your account balance is 100.00 USD')
                ->assertSee('Your account balance is 100.00 USD')
                ->type('#entry', 'can I withdraw 50 usd')
                ->press('#send')
                ->waitForText('Your account balance is 50.00 USD')
                ->assertSee('Your account balance is 50.00 USD')
            ;
        });
    }
}
