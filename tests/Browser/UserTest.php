<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;
class UserTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    private $newUser = [
        "head" => [
            "id" => null,
            "name" => "head - name",
            "password" => "123456789"
        ],
        "op" => [
            "id" => null,
            "name" => "op - name",
            "password" => "123456789"
        ],
        "team" => [
            "id" => null,
            "name" => "team - name",
            "password" => "123456789"
        ]
    ];

    public function testUserManagement() {
        $this->browse(function (Browser $browser) {
            $this->login($browser, "111111", "123456789", "整備予定実施表");
            $this->visitByClick($browser, ".router-item-3", '.user-management', 'ユーザ管理'); // click on icon of user management
            $browser->waitFor('#table-user-management', 3);
            $this->newUser['head']['id'] = rand(100001, 999999);
            $this->newUser['op']['id'] = rand(100001, 999999);
            $this->newUser['team']['id'] = rand(100001, 999999);
            $this->testUserCreate($browser, $this->newUser['head']['id'], $this->newUser['head']['name'], $this->newUser['head']['password'], function () use($browser) {
                $browser->assertDisabled('select[class-name="user-management-select base"]')->pause(1500);
                $browser->select('select[class-name="user-management-select user-role"]', 1)->pause(1500); // select role headquater
            });

            $this->testUserCreate($browser, $this->newUser['op']['id'], $this->newUser['op']['name'], $this->newUser['op']['password'], function () use($browser) {
                $browser->assertDisabled('select[class-name="user-management-select base"]')->pause(1500);
                $browser->select('select[class-name="user-management-select user-role"]', 2)->pause(1500); // select role operator
            });

            $this->testUserCreate($browser, $this->newUser['team']['id'], $this->newUser['team']['name'], $this->newUser['team']['password'], function () use($browser) {
                $browser->select('select[class-name="user-management-select user-role"]', 3)->pause(1500); // select role Team
                $browser->select('select[class-name="user-management-select base"]', "")->pause(1500); // no select department
                $browser->type('input[class="form-control user-management-input user-id"]', "111111")->pause(500); // exists user
                $browser->type('input[class="form-control user-management-input user-name"]', "Nguyen Tien Nam - Developer - User Name: " . rand(1, 1500))->pause(500);
                $browser->type('input[class="form-control user-management-input pwd"]', "123456789")->pause(500);

                $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('拠点が必要です。')->pause(1500); // check valid department required

                $browser->clear('input[class="form-control user-management-input user-id"]')->pause(200);
                $browser->clear('input[class="form-control user-management-input user-name"]')->pause(200);
                $browser->clear('input[class="form-control user-management-input pwd"]')->pause(200);
                $browser->select('select[class-name="user-management-select base"]', 1)->pause(1500);
            });
            $this->testOrderBy($browser);
            $this->testFilter($browser);
            $this->testUserDelete($browser);
            // $this->testUserUpdate($browser);
        });
    }

    private function testUserCreate($browser, $id, $name, $password, $callback) {

        $browser->press('button[class="btn btn-secondary maintenance-btn-default maintenance-pdf-btn"]')->pause(1500);
        $browser->select('select[class-name="user-management-select user-role"]', "")->pause(1500); // no select role
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('ユーザー権限が必要です。')->pause(1500); // check valid role required

        call_user_func($callback);

        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('ユーザーIDが必要です。')->pause(1500); // check valid id role required

        $browser->type('input[class="form-control user-management-input user-id"]', "111111")->pause(1500); // exists user

        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('ユーザー名が必要です。')->pause(1500); // check User Name required
        $browser->type('input[class="form-control user-management-input user-name"]', $name)->pause(500);

        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('パスワードが必要です。')->pause(1500); // check Password required
        $browser->type('input[class="form-control user-management-input pwd"]', $password);

        $browser->press('#btn-save')->waitFor('.toast-body', 1)
        ->waitFor('.toast-body', 1)
        ->assertSee('指定のユーザーIDは既に使用されています。')
        ->pause(1500); // user exits
        $browser->clear('input[class="form-control user-management-input user-id"]')->pause(300);
        $browser->type('input[class="form-control user-management-input user-id"]', "111111111111111")->pause(1500); // exists user

        $browser->press('#btn-save')->waitFor('.toast-body', 1)
        ->waitFor('.toast-body', 1)
        ->assertSee('ユーザIDは1文字以上8文字以内である必要があります。')
        ->pause(1500); // id too length
        $browser->clear('input[class="form-control user-management-input user-id"]')->pause(300);
        $browser->type('input[class="form-control user-management-input user-id"]', "111111")->pause(1500);

        $browser->clear('input[class="form-control user-management-input pwd"]')->pause(300);
        $browser->type('input[class="form-control user-management-input pwd"]', "123")->pause(500); // Password length must be at least 8 characters.
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('パスワードは8文字以上16文字以内である必要があります。')->pause(1500); // check Password required


        $browser->clear('input[class="form-control user-management-input pwd"]')->pause(300);
        $browser->type('input[class="form-control user-management-input pwd"]', "1234567890000000000000000")->pause(500); ; // Password length <16
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('パスワードは8文字以上16文字以内である必要があります。')->pause(1500); // check Password required

        $browser->clear('input[class="form-control user-management-input pwd"]')->pause(300);
        $browser->type('input[class="form-control user-management-input pwd"]', $password)->pause(500); ; // Password length <16

        $browser->type('input[class="form-control user-management-input user-id"]', $id)->pause(3000); // exists user
        $browser->press('#btn-save')->waitFor('.toast-body', 1)
        ->assertSee('成功')
        ->pause(2000); // user exits
    }

    private function testOrderBy($browser) {
        $orderByButtons = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="text-center"]'));
        $counting = 0;
        foreach ($orderByButtons as $key => $btn) {
            $btn->click();
            $browser->pause(2000);
            $btn->click();
            $browser->pause(2000);
            $counting++;
            if ($counting == 4) break;
        }
        $browser->pause(2000);
    }

    private function testFilter($browser) {
        $browser->pause(2000);
        $browser->press('.filter-title')->pause(300);

        $chks_filter = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="chk_filter"]'));
        $chks_filter[0]->click();

        $browser->pause(1000);
        $browser->type('#filter-by-user-id', "111111")->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $browser->pause(2000);
        $browser->press('.text-clear-all')->pause(1000);
        $chks_filter[1]->click();
        $browser->type('#filter-by-user-name', "User Team")->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $browser->press('.text-clear-all')->pause(1000);

        $chks_filter[0]->click();
        $chks_filter[1]->click();
        $browser->type('#filter-by-user-id', "111111")->pause(500);
        $browser->type('#filter-by-user-name', "User Team")->pause(500);
        $browser->press('.btn-summit-filter')->pause(2000)->assertSee('データがありません。')->pause(2000);

        $chks_filter[0]->click();
        $chks_filter[1]->click();
        $browser->press('.text-clear-all')->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $browser->press('.filter-title')->pause(300);
    }

    // private function testUserUpdate($browser) {
    //     $browser->press('span[class="btn-function btn-edit"]')->pause(1000);
    //     $browser->pause(2000);
    // }

    // class="modal-content modal-custom-body"
    private function testUserDelete($browser) {
        $browser->press('.filter-title')->pause(300);
        $chks_filter = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="chk_filter"]'));
        $chks_filter[0]->click();
        $browser->pause(1000);
        $browser->type('#filter-by-user-id', $this->newUser['team']['id'])->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $name = $this->newUser['team']['name'];
        $shouldSee = "【{$name}】を削除してよろしいですか?";
        $browser->press('.btn-remove')->waitFor('footer[class="modal-footer modal-custom-footer"]', 2)->assertSee($shouldSee)->pause(1000);
        $browser->press('.btn-cancel')->pause(1000);
        $browser->press('.btn-remove')->waitFor('footer[class="modal-footer modal-custom-footer"]', 2)->assertSee($shouldSee)->pause(1000);
        $browser->press('.btn-apply')->waitUntilMissing('footer[class="modal-footer modal-custom-footer"]')->waitFor('.toast-body', 1)->assertSee('成功')->pause(2000);


        $browser->clear('#filter-by-user-id')->pause(1000);
        $browser->type('#filter-by-user-id', $this->newUser['op']['id'])->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $name = $this->newUser['op']['name'];
        $shouldSee = "【{$name}】を削除してよろしいですか?";
        $browser->press('.btn-remove')->waitFor('footer[class="modal-footer modal-custom-footer"]', 2)->assertSee($shouldSee)->pause(1000);
        $browser->press('.btn-apply')->waitUntilMissing('footer[class="modal-footer modal-custom-footer"]')->waitFor('.toast-body', 1)->assertSee('成功')->pause(2000);

        $browser->clear('#filter-by-user-id')->pause(1000);
        $browser->type('#filter-by-user-id', $this->newUser['head']['id'])->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $name = $this->newUser['head']['name'];
        $shouldSee = "【{$name}】を削除してよろしいですか?";
        $browser->press('.btn-remove')->waitFor('footer[class="modal-footer modal-custom-footer"]', 2)->assertSee($shouldSee)->pause(1000);
        $browser->press('.btn-apply')->waitUntilMissing('footer[class="modal-footer modal-custom-footer"]')->waitFor('.toast-body', 1)->assertSee('成功')->pause(2000);

        $browser->press('.text-clear-all')->pause(500);
        $browser->press('.btn-summit-filter')->pause(1000);
        $browser->press('.filter-title')->pause(300);
    }
}

// $browser->select('select[class="custom-select custom-select-md accessories-management-select tonnage"]', $arrayOfTonnage[array_rand([3,4,6,12])])->pause(1000); //select tonnage
// $browser->pause(1000);
// $browser->select('.passed-years', "")->pause(1000);
// $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('経過年数が必要です。')->pause(1000); // check validate passed_year
// chk_filter
/// id="filter-by-user-id"
