<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
class AccessoryTest extends DuskTestCase
{
    public function testLoginAndVisitAccessoryIndex() {
        $this->browse(function (Browser $browser) {
            $this->login($browser, "111111", "123456789", "整備予定実施表");
            $this->visitByClick($browser, ".router-item-2", '.accessory-management', '交換部品管理', 2000);
            $this->createAccessory($browser);
            $this->checkUpdateScreen($browser);
            $this->checkPaginate($browser);
            $this->checkFilter($browser);
            $browser->pause(2000);
            $this->checkOrderBy($browser);
            $browser->pause(2000);
            $this->searchAndDelete($browser);
            $this->logOut($browser);
        });
    }

    private function checkFilter($browser) {
        $valuesInTable = $browser->driver->findElements(WebDriverBy::xpath('//*[@aria-colindex="1"]'));
        $browser->pause(200);
        $textSearch = $valuesInTable[1]->getText();

        $browser->press('.filter-title')->pause(300);
        $browser->check('.chk_filter')->pause(500); // open filter

        $browser->type('#filter-by-accessory-name', "Try to search")->pause(500); // filter no result
        $browser->press('.btn-summit-filter')->pause(1000)->assertSee('データがありません。')->pause(2000);

        $browser->type('#filter-by-accessory-name', $textSearch)->pause(500); //filter by name, should have result
        $browser->press('.btn-summit-filter')->pause(2000);
        $browser->assertSee($textSearch)->pause(500);

        $browser->press('.text-clear-all')->pause(200); // cealr filter
        $browser->press('.btn-summit-filter')->pause(2000);
        $browser->press('.filter-title')->pause(300);
    }

    private function checkPaginate($browser) {
        $btns = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="page-content-wrapper"]/div[2]/div/div/div/div[6]/ul/li'));
        $count = 0;
        foreach ($btns as $key => $btn) {
            $count++;
            if ($count == 4) break;
            $btn->click();
            $browser->pause(1500);
        }
        if (count($btns) > 1) {
            $browser->select('.custom-select', 500); // select 500 record
            $browser->pause(2000);
            $browser->select('.custom-select', 20); // select 20 record
            $browser->pause(2000);
        }
    }

    private function checkOrderBy($browser) {
        $orderByBtns = $browser->driver->findElements(WebDriverBy::xpath('//*[@role="columnheader"]'));
        $browser->pause(1000);
        $orderByBtns[1]->click(); // change order by
        $browser->pause(3000);
        $orderByBtns[2]->click(); // change order by
        $browser->pause(3000);
        $orderByBtns[3]->click(); // change order by
        $browser->pause(3000);
        $orderByBtns[4]->click(); // change order by
        $browser->pause(3000);
    }

    private function checkUpdateScreen($browser) {
        $browser->press('.btn-edit')->pause(3000);
        $browser->click('#btn-back')->pause(2000);
        $editButtons = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="btn-function btn-edit"]'));
        $editButtons[0]->click();
        $browser->pause(2000);

        $browser->clear('input[class="form-control accessories-management-input accessory-name"]')->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 2)->assertSee('交換部品名は必須です。')->pause(1000); // check validate name

        $browser->type('input[class="form-control accessories-management-input accessory-name"]', "Added By the order of the peaky blinders"); // change name
        $browser->pause(1000);

        $browser->select('select[class="custom-select custom-select-md accessories-management-select tonnage"]', "")->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 2)->assertSee('トン数が必要です。')->pause(1000); // check validate tonnage

        $browser->pause(1000);
        $arrayOfTonnage = [3,4,6,12];
        $browser->select('select[class="custom-select custom-select-md accessories-management-select tonnage"]', $arrayOfTonnage[array_rand([3,4,6,12])])->pause(1000); //select tonnage

        $browser->pause(1000);
        $browser->select('.passed-years', "")->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 2)->assertSee('経過年数が必要です。')->pause(1000); // check validate passed_year

        $browser->pause(1000);
        $browser->select('.passed-years', rand(0,10))->pause(1000); //select passed_year

        $browser->pause(1000);
        $browser->clear('input[class="form-control accessories-management-input mileage"]')->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 2)->assertSee('走行距離が必要です。')->pause(1000); // check validate passed_year

        $browser->pause(1000);
        $browser->type('input[class="form-control accessories-management-input mileage"]', rand(10000,30000))->pause(500); // change mileage

        $browser->type('#remark', "Remark added by the order of the peaky blinders")->pause(1000); // change remark
        $browser->press('#btn-save')->waitFor('.toast-body', 2)->assertSee('成功')->pause(2000);
    }

    private function createAccessory($browser) {

        $browser->press('button[class="btn btn-secondary maintenance-btn-default maintenance-pdf-btn"]')->pause(2000);

        $browser->clear('input[class="form-control accessories-management-input accessory-name"]')->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('交換部品名は必須です。')->pause(1000); // check validate name

        $browser->type('input[class="form-control accessories-management-input accessory-name"]', "A new record"); // change name
        $browser->pause(1000);

        $browser->select('select[class="custom-select custom-select-md accessories-management-select tonnage"]', "")->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 2)->assertSee('トン数が必要です。')->pause(1000); // check validate tonnage

        $browser->pause(1000);
        $arrayOfTonnage = [3,4,6,12];
        $browser->select('select[class="custom-select custom-select-md accessories-management-select tonnage"]', $arrayOfTonnage[array_rand([3,4,6,12])])->pause(1000); //select tonnage

        $browser->pause(1000);
        $browser->select('.passed-years', "")->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('経過年数が必要です。')->pause(1000); // check validate passed_year

        $browser->pause(1000);
        $browser->select('.passed-years', rand(0,10))->pause(1000); //select passed_year

        $browser->pause(1000);
        $browser->clear('input[class="form-control accessories-management-input mileage"]')->pause(1000);
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('走行距離が必要です。')->pause(1000); // check validate passed_year

        $browser->pause(1000);
        $browser->type('input[class="form-control accessories-management-input mileage"]', rand(10000,30000))->pause(500); // change mileage

        $browser->type('#remark', "A new record remark")->pause(1000); // change remark
        $browser->press('#btn-save')->waitFor('.toast-body', 1)->assertSee('成功')->pause(2000);
    }

    private function searchAndDelete($browser) {
        $SearchName = "A new record";

        $browser->press('.filter-title')->pause(500);
        $browser->check('.chk_filter')->pause(500); // open filter

        $browser->type('#filter-by-accessory-name', $SearchName)->pause(500); //filter by name, should have result
        $browser->press('.btn-summit-filter')->pause(2000);
        $browser->assertSee($SearchName)->pause(2000);

        $shouldSee = "【{$SearchName}】を削除してよろしいですか?";
        $browser->press('span[class="btn-function btn-remove"]')->waitFor('.modal-md', 2)->assertSee($shouldSee)->pause(2000);

        $browser->press('button[class="btn modal-btn btn-cancel btn-secondary"]')->waitUntilMissing('.modal-md')->pause(2000);

        $shouldSee = "【{$SearchName}】を削除してよろしいですか?";
        $browser->press('span[class="btn-function btn-remove"]')->waitFor('.modal-md', 2)->assertSee($shouldSee)->pause(2000);

        $browser->press('button[class="btn modal-btn btn-apply btn-secondary"]')->waitUntilMissing('.modal-md')->waitFor('.toast-body', 1)->assertSee('成功')->pause(2000);
    }
}
