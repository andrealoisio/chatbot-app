<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LogoutTest extends DuskTestCase
{
    /**
     * Verify logout process
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
                ->type('#entry', 'logout')
                ->press('#send')
                ->waitForText('logged out')
                ->assertSee('logged out')
            ;
        });
    }
}
