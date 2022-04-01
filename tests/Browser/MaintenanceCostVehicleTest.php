<?php

namespace Tests\Browser;

use Carbon\Carbon;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MaintenanceCostVehicleTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testGeneral()
    {
        $this->browse(function ($browser) {
            $this->login($browser, "111111", "123456789", "整備予定実施表");
            $this->goToDetail($browser);
            $this->backButtonDetail($browser);
            $this->goToDetail($browser);
            $this->showModalHistory($browser);
            $this->testChangeListYear($browser);
            $this->nextToEdit($browser);
            $this->showModalHistory($browser);
            $this->saveData($browser);
        });
    }

    // case go to detail
    public function goToDetail($browser)
    {
        $browser->pause(2000)->click('.btn-schedule-result')->pause(4000);

        // go to department test
        $browser->with('#department-selector', function ($table) {
            $elems = $table->pause(1000)->elements('li');
            foreach ($elems as $key => $elem) {
                if ($key > 0 && $key < 5 && $elem->getText() !== $table->text('button') && $table->text('button') !== '横浜第一') {
                    $table->click("#department-selector");
                    //(new WebDriverActions($table->driver))->click($elem)->perform();
                    $elem->click();
                    $table->pause(2000);
                }
            }
        });

        // filter button detail and click to view detail vehicle data
        $browser->with('#table-schedule-result', function ($table) {
            $elem = $table->pause(1000)->element('.detail');
            $action = new WebDriverActions($table->driver);
            $action->moveToElement($elem)->click()->perform();
            $table->pause(2000);
        });

        // in view detail check assert See
        $browser->waitForText('整備実績')
            ->assertSee('車両No')
            ->assertSee('車両データ')
            ->assertSee('更新履歴を見る')
            ->assertSee('走行距離 (km)')
            ->assertSee('更新履歴を見る')
            ->assertSee('整備データ')
            ->assertSee('年')
            ->assertSee('戻る')
            ->assertSee('編集');
    }

    // test back button
    public function backButtonDetail($browser)
    {
        $browser->click('#btn-back')->pause(4000);
        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });
    }

    //test next to view edit
    public function nextToEdit($browser)
    {
        $browser->pause(1000)->click('#btn-edit')->pause(2000);
        $browser->waitForText('整備実績')
            ->assertSee('車両No')
            ->assertSee('車両データ')
            ->assertSee('更新履歴を見る')
            ->assertSee('走行距離 (km)')
            ->assertSee('更新履歴を見る')
            ->assertSee('整備データ')
            ->assertSee('年')
            ->assertSee('戻る')
            ->assertSee('保存');
    }

    // test show view modal history
    public function showModalHistory($browser)
    {
        // show view plate history
        $browser->click('.modal-history')->pause(2000);
        $browser->with('#modal-history', function ($table) {
            $table->assertSee('更新日')
                ->assertSee('車両No')
                ->assertSee('閉じる');
            $table->click('.btn');
        });

        // show view mileage history
        $browser->click('.modal-mileage-history')->pause(2000);
        $browser->with('#modal-mileage-history', function ($table) {
            $table->assertSee('更新日')
                ->assertSee('走行距離 (km)')
                ->assertSee('更新距離 (km)')
                ->assertSee('閉じる');
            $table->click('.btn');
        });
    }

    //test change list year
    public function testChangeListYear($browser)
    {
        //go to view next year
        $browser->pause(1000)->scrollIntoView('#get-next-year')->click('#get-next-year')->pause(3000)->assertSee('年');
        // back view lst year
        $browser->click('#get-prev-year')->pause(3000)->assertSee('年');

        $browser->with('.maintenance-data-table', function ($table) use ($browser){
            $elems = $table->pause(1000)->elements('.show-all');
            foreach ($elems as $key => $elem) {
                $action = new WebDriverActions($table->driver);
                $action->moveToElement($elem)->click()->perform();
                $table->pause(1000);
                $browser->assertPresent('.tooltip-inner');
                $table->pause(1000);
            }
        });
    }

    //test save vehicle data
    public function saveData($browser)
    {
        $date = Carbon::now()->format('Y-m-d');
        $browser->pause(1000)
            ->press('@glass_name')
            ->type('@glass_name', 'glass name')
            ->press('@glass_name')
            ->type('@glass_date', $date)
            ->type('@body_shutter_name', 'body shutter name')
            ->type('@body_shutter_date', $date)
            ->type('@camera_monitor_name', 'camera monitor name')
            ->type('@camera_monitor_date', $date)
            ->type('@gate_name', 'gate name')
            ->type('@gate_date', $date)
            ->type('@others_name', 'others name')
            ->type('@others_date', $date)
            ->type('@remark_1', 'remark 1')
            ->type('@remark_2', 'remark 2');
        $browser->click('#btn-save')
            ->waitFor('.toast-body', 2)
            ->assertSee('成功');
    }
}


