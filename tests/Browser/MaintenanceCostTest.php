<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;
use Illuminate\Support\Str;
class MaintenanceCostTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, "111111", "123456789", "整備予定実施表"); //correct
            $this->visitByClick($browser, ".router-item-1", '.maintenance-cost', '整備実績'); // click on icon of maintenance cost
            $browser->pause(100);
            $browser->assertMissing(".toast-body");
            $browser->pause(1000);
            $dataTable = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="table-maintenance-cost"]/tbody/tr'));
            // if (count($dataTable) > 0) {
            //     for ($i = 1; $i <= count($dataTable); $i++) {
            //         $mtType = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="table-maintenance-cost"]/tbody/tr['. $i . ']/td[3]/span'));
            //         if ($mtType[0]->getText() == "3ヶ月点検" || $mtType[0]->getText() == "12ヶ月点検") {
            //             $this->testChangeRegisterPeriodicInspections($browser, $i);
            //             $this->visitByClick($browser, ".router-item-1", '.maintenance-cost', '整備実績');
            //         } else if ($mtType[0]->getText() == "部品交換") {
            //             $this->testRegisterAccessorySchedule($browser, $i);
            //             $this->visitByClick($browser, ".router-item-1", '.maintenance-cost', '整備実績');
            //         }
            //     }
            //     // $this->testChangeRegisterPeriodicInspections($browser);
            // }
            // $this->registerOther($browser);
            // $this->orderBy($browser);
            $this->filter($browser);
            $this->logOut($browser);
        });
    }

    private function testChangeRegisterPeriodicInspections($browser, $index) {
        $button = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="table-maintenance-cost"]/tbody/tr['. $index . ']/td[6]/button'));
        $button[0]->click();
        $browser->pause(1000);
        $url = $browser->driver->getCurrentURL();
        $browser->assertSee('整備種別');
        $mtType = $browser->value('input[class-name="maintenance-type-input maintenance-type"]');
        if ($mtType == '3ヶ月点検' || $mtType == '12ヶ月点検') {
            $this->assertTrue(true);
        } else $this->assertTrue(false, 'maintenance type missmatch "3ヶ月点検" or "12ヶ月点検"');

        if (Str::contains($url, 'periodic-inspections/detail')) { // detail, edit
            $browser->press('#btn-edit');
            $browser->pause(1000);
            $browser->clear('input[class-name="mileage-current-time-input mileage-current-time"]');
            $browser->type('input[class-name="mileage-current-time-input mileage-current-time"]', "700000");
            $browser->pause(1000);
            $deleteAccessories = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="btn btn-del-accessories btn-secondary"]'));
            $deleteAccessories[0]->click();
            $browser->pause(1000);
            $deleteAccessories[1]->click();
            $browser->pause(1000);
            $optionsAccessories = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="custom-select"]/option'));
            $this->selectAccessory($browser, 2000, 4, $optionsAccessories);
            $browser->pause(5000);
        } else if (Str::contains($url, 'periodic-inspections')) { // register
            $browser->pause(1000);
            $browser->press('#btn-register')->waitFor('.toast-body', 1)->assertSee('整備担当が必要です。')->pause(1000); // check valid charge type
            $browser->pause(1000);
            $browser->radio('div[class="custom-control custom-control-inline custom-radio"]', 1);

            $browser->type('input[class-name="maintenance-type-input maintenance-date"]', "2000"); // validate wrong Y-m-d
            $browser->press('#btn-register')->waitFor('.toast-body', 1)
            ->assertSee('正しい形式の整備実施日が必用です。')
            ->pause(2000);

            $browser->type('input[class-name="maintenance-type-input maintenance-date"]', "2022-02"); // validate wrong Y-m-d
            $browser->press('#btn-register')->waitFor('.toast-body', 1)
            ->assertSee('正しい形式の整備実施日が必用です。')
            ->pause(1000);

            $browser->type('input[class-name="maintenance-type-input maintenance-date"]', "2022-02-14"); // good input
            $browser->press('#btn-register')->waitFor('.toast-body', 1)
            ->assertSee('走行距離（現在時）が必要です。') // no input to mileage
            ->pause(1000);

            $browser->type('input[class-name="mileage-current-time-input mileage-current-time"]', "1000");
            $browser->press('#btn-register')->waitFor('.toast-body', 1)
            ->assertSee('走行距離(整備時)は走行距離(前回)以上である必要があります。') // bad input to mileage, current mileage must biger than last mileage
            ->pause(1000);

            $browser->clear('input[class-name="mileage-current-time-input mileage-current-time"]');
            $browser->type('input[class-name="mileage-current-time-input mileage-current-time"]', "700000");

            $optionsAccessories = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="custom-select"]/option'));
            $this->selectAccessory($browser, 2000, 4, $optionsAccessories, true);
            $browser->pause(500);
            $this->selectAccessory($browser, 1500, 15, $optionsAccessories);
            $browser->pause(500);
            $this->selectAccessory($browser, 1500, 15, $optionsAccessories);
            $browser->pause(500);
            $this->selectAccessory($browser, 2000, 4, $optionsAccessories);
            $browser->pause(5000);
        }
        $browser->pause(3000);
    }

    private function testRegisterAccessorySchedule($browser, $index) {
        $button = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="table-maintenance-cost"]/tbody/tr['. $index . ']/td[6]/button'));
        $button[0]->click();
        $browser->pause(1000);
        $url = $browser->driver->getCurrentURL();
        $browser->assertSee('整備種別');
        $browser->pause(2000);

        $mtType = $browser->value('input[class-name="maintenance-type-input maintenance-type"]');
        if ($mtType == '部品交換') {
            $this->assertTrue(true);
        } else $this->assertTrue(false, 'maintenance type missmatch "部品交換"');
        $browser->press('.warning-desc');
        $replacedData = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="table b-table"]/tbody'));
        if (count($replacedData) > 1) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, "replaced accessories was blank"); // check replaced list
        }
        if (Str::contains($url, 'accessory-schedule//detail')) { // detail, edit

        } else if (Str::contains($url, 'accessory-schedule')) {
            $browser->pause(2000);
            $browser->press('.btn-warning');
            $browser->pause(1500);
            $browser->radio('div[class="custom-control custom-control-inline custom-radio"]', 2);
            $browser->pause(1500);
            $browser->type('input[class-name="maintenance-type-input maintenance-date"]', "2022-02"); // good input
            $browser->pause(3000);
            $optionsAccessories = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="custom-select"]/option'));
            $this->selectAccessory($browser, 2000, 4, $optionsAccessories, true);
            $browser->pause(1000);
            $this->selectAccessory($browser, 1500, 15, $optionsAccessories);
            $browser->pause(1000);
        }
    }

    private function registerOther($browser) {
        $browser->pause(1000);
        $browser->press('.maintenance-btn-default');
        $browser->pause(1000);
        $plates = $browser->driver->findElements(WebDriverBy::xpath('//*[@class-name="no-number-plate-input no-number-plate"]/option'));
        $browser->pause(500);
        $plates[rand(1, count($plates) - 1)]->click();
        $browser->pause(2000);
        $browser->radio('div[class="custom-control custom-control-inline custom-radio"]', 2);
        $browser->pause(1500);
        $browser->type('input[class-name="maintenance-type-input maintenance-date"]', "2022-02-20"); // good input
        $browser->pause(1500);
        $optionsAccessories = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="custom-select"]/option'));
        $this->selectAccessory($browser, 2000, 4, $optionsAccessories, true);
        $browser->pause(1000);
        $this->selectAccessory($browser, 1500, 15, $optionsAccessories);
        $browser->pause(1000);
    }

    private function orderBy($browser) {
        $header = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="table-maintenance-cost"]/thead/tr[1]/th'));
        for ($i=0; $i < count($header) - 1; $i++) {
            $header[$i]->click();
            $browser->waitUntilMissing('.modal-md')->pause(1500);
            $header[$i]->click();
            $browser->waitUntilMissing('.modal-md')->pause(1500);
        }
    }

    private function filter($browser) {
        $browser->press('.filter-title');
        $browser->pause(1000);
        $browser->check('.chk_filter');
        $browser->pause(1000);
        $plates = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="filter-by-number-plate"]/option'));
        $plates[rand(0, count($plates) - 1)]->click();
        $browser->pause(1000);
        $browser->click('.btn-summit-filter');
        $browser->pause(2000);
        $browser->uncheck('.chk_filter');
        $browser->pause(500);
        $browser->click('.btn-summit-filter');
        $browser->pause(2000);
        $browser->check('.check_filter_maintenance_scheduled_date');

        $calendarSchedule = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="fad fa-calendar-day"]'));
        $calendarSchedule[0]->click();
        $browser->pause(2000);
        $browser->driver->findElements(WebDriverBy::xpath('//*[@data-date="2022-03-01"]'))[0]->click();
        $browser->pause(1000);
        $calendarSchedule[1]->click();
        $browser->driver->findElements(WebDriverBy::xpath('//*[@data-date="2022-04-02"]'))[0]->click();
        $browser->pause(500);
        $browser->click('.btn-summit-filter');
        $browser->pause(2000);
        $browser->uncheck('.check_filter_maintenance_scheduled_date');
        $browser->pause(5000);
    }

    private function selectAccessory($browser, $price, $amonut, $optionsAccessories, $other = false) {
        $index = rand(1, count($optionsAccessories) - 2);
        if ($index == count($optionsAccessories) - 1) $index--;
        if ($other) {
            $index = count($optionsAccessories) - 1;
        }
        $optionsAccessories[$index]->click();
        if ($other) {
            $amount = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="quantity"]/input'));
            $amount[0]->sendKeys("Other Name Accessory");
        }

        $amount = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="quantity"]/input'));
        $amount[1]->sendKeys($amonut);

        $amount = $browser->driver->findElements(WebDriverBy::xpath('//*[@class="price"]/input'));
        $amount[0]->sendKeys($price);
        $browser->press('.btn-add-accessories');
    }
}
