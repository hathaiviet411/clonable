<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RoleTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testGeneral()
    {
        $this->browse(function ($browser) {
            $browser->visit('/')->waitFor('.login-btn');
            $this->caseRoleAdmin($browser);
            $this->caseRoleTeam($browser);
        });
    }

    public function caseRoleAdmin($browser)
    {
        $browser->visit('/')->waitFor('.login-btn')
            ->type('#user_id', '111111')
            ->type('#password', '123456789')->press('.login-btn')
            ->waitFor('.toast-header')->assertSee('成功')->assertPathIs('/')
            ->assertSeeIn('.toast-header', '成功')->assertPathIs('/')
            ->waitFor('#sidebar-wrapper')
            ->assertSee('整備予定実施表')
            ->assertSee('整備実績')
            ->assertSee('交換部品管理')
            ->assertSee('ユーザ管理')
            ->press('.btn-logout')
            ->assertPathIs('/')
        ;
    }

    public function caseRoleTeam($browser)
    {
        $browser->visit('/')->waitFor('.login-btn')
            ->type('#user_id', '666666')
            ->type('#password', '123456789')->press('.login-btn')
            ->waitFor('.toast-header')->assertSee('成功')->assertPathIs('/')
            ->assertSeeIn('.toast-header', '成功')->assertPathIs('/')
            ->waitFor('#sidebar-wrapper')
            ->assertSee('整備予定実施表')
            ->assertSee('整備実績')
            ->assertDontSee('交換部品管理')
            ->assertDontSee('ユーザ管理')
            ->press('.btn-logout')
            ->assertPathIs('/')
        ;
    }

}


