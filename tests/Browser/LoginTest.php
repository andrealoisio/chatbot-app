<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class LoginTest extends DuskTestCase
{
    /**
     * Shoul show help message
     *
     * @return void
     */
    public function testShowHelp()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Chatbot')
                ->type('#entry', 'help')
                ->press('#send')
                ->assertSee('You can log-in or sign-up');
        });
    }
    /**
     * Verify login process
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
            ;
        });
    }
    /**
     * Verify register process
     *
     * @return void
     */
    public function testRegister()
    {
        $this->browse(function (Browser $browser) {
            User::where('email', 'john-doe-2@test.com')->delete();
            $browser
                ->visit('/')
                ->type('#entry', 'may i sign-up?')
                ->press('#send')
                ->type('#entry', 'John Doe')
                ->press('#send')
                ->type('#entry', 'brl')
                ->press('#send')
                ->type('#entry', 'john-doe-2@test.com')
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->type('#entry', 'password')
                ->press('#send')
                ->waitForText('Signed up successfully! You can now log in.')
                ->assertSee('Signed up successfully! You can now log in.')
            ;
        });
    }
}
