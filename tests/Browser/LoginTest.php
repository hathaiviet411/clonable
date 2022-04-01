<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A scenario of automation test, try to login by super admin account
     * After login, application will visit all page to check the response working well
     * After visit all page by url, will move to page one by one by click.
     * Almost done, LogOut is the last step
     * @return void
     */
    public function testLoginAndVisitAllPageUntilLogOut() {
        $this->browse(function (Browser $browser) {
            $this->loginFaild($browser, "111111", "1234567890", "server.emp_code_or_password_incorrect"); // wrong password
            $this->login($browser, "111111", "123456789", "整備予定実施表"); //correct
            $this->visitPage($browser, "/maintenance-cost/index", "整備実績"); // view screen maintenance cost
            $this->visitPage($browser, "/accessory-management/index", "交換部品管理"); //view accessory master
            $this->visitPage($browser, "/user-management/index", "ユーザ管理"); // view user master
            $this->visitPage($browser, "/maintenance-schedule-results/index", "整備予定実施表"); // view schedule, defauly is Periodic Inspection Schedule Table
            $this->visitByClick($browser, '.btn-schedule-result', '#table-schedule-result', '整備未完了アラート'); // on schedule screen, move to accessory schedule
            $this->visitByClick($browser, ".router-item-2", '.accessory-management', '交換部品管理'); // click on icon of accessiry management
            $this->visitByClick($browser, ".router-item-1", '.maintenance-cost', '整備実績'); // click on icon of maintenance cost
            $this->visitByClick($browser, ".router-item-0", '.maintenance-schedule-results', '整備予定実施表'); // click icon of schedule
            $this->visitByClick($browser, ".router-item-3", '.user-management', 'ユーザ管理'); // click on icon of user management
            $this->logOut($browser); // logout
        });
    }
}
