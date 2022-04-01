<?php

namespace Tests\Browser;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AccessoryScheduleTableTest extends DuskTestCase
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
            $this->caseShowList($browser);
            $this->caseDownloadPDF($browser);
            $this->caseFilter($browser);
            $this->caseFilterHeader($browser);
            $this->caseMonthTooltip($browser);
        });
    }

    //case show list table schedule result
    public function caseShowList($browser)
    {
        // go to page with click menu
        $browser->pause(2000)
            ->waitFor('#sidebar-wrapper')
            ->assertSee('整備予定実施表')
            ->click('.fa-calendar-day')
            ->pause(2000)
            ->assertSee('部品交換計画実施表')
            ->click('.btn-schedule-result')
            ->pause(2000)
            ->assertSee('交換完了')
            ->assertSee('交換未完了アラート');
        //assert see table schedule result
        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No');
            $table->assertSee('月');
        });
    }

    //case filter table schedule result
    public function caseFilter($browser)
    {
        //go to page anh filter by plate, and accessory name
        $browser->click('.btn-schedule-result')->pause(4000);
        $browser->with('.header-filter', function ($element) {
            $element->click("#collapsed-show-hide-filter")
                ->pause(1000)
                ->check("@filter-by-number-plate")->pause(2000)
                ->assertChecked("@filter-by-number-plate")
                ->releaseMouse()->select('#filter-by-number-plate')->pause(2000)
                ->click(".btn-summit-filter")->pause(5000);
        });

        //assert see table schedule result
        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });

        // filter by accessory name
        $browser->with('#zone-filter', function ($element) {
            $element->check("@filter-by-accessory-name")->pause(2000)
                ->assertChecked("@filter-by-accessory-name")
                ->releaseMouse()->type('#filter-by-accessory-name', 'filter by name')->pause(2000)
                ->click(".btn-summit-filter")->pause(5000);
        });

        //assert see table schedule result
        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });

        $browser->with('#zone-filter', function ($element) {
            $element->click(".text-clear-all")->pause(2000)
                ->assertNotChecked("@filter-by-number-plate")
                ->assertNotChecked("@filter-by-accessory-name")->pause(2000)
                ->click(".btn-summit-filter")->pause(5000);
        });

        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });
    }

    // filter with header
    public function caseFilterHeader($browser)
    {
        //go to header
        $browser->click('.btn-schedule-result')->pause(4000);
        $browser->pause(2000)->scrollIntoView('.nav-bar');
        //filter by change year
        $browser->with('#date-time-selector', function ($table) {
            $elems = $table->pause(1000)->elements('li');
            foreach ($elems as $key => $elem) {
                if ($key > 1 && $key < 5) {
                    $table->click("#date-time-selector");
                    $elem->click();
                    $table->pause(1000);
                }
            }
        });

        $browser->pause(3000)->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });

        //filter by change department
        $browser->with('#department-selector', function ($table) {
            $elems = $table->pause(1000)->elements('li');
            foreach ($elems as $key => $elem) {
                if ($key > 0 && $key < 5 && $elem->getText() !== $table->text('button')) {
                    $table->click("#department-selector");
                    //(new WebDriverActions($table->driver))->click($elem)->perform();
                    $elem->click();
                    $table->pause(1000);
                }
            }
        });
        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });
    }

    //case show list accessory in cell
    public function caseMonthTooltip($browser)
    {
        $browser->pause(2000)->click('.btn-schedule-result')->pause(4000);

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

        $browser->with('#table-schedule-result', function ($table) {
            $elems = $table->pause(1000)->elements('.month-tooltip');
            $listIdTooltip = [];
            foreach ($elems as $key => $elem) {
                $elemLi = $elem->findElement(WebDriverBy::cssSelector('.fa-caret-circle-down'));
                //array_push($listIdTooltip, trim($elemLi->getAttribute('id')));
                $action = new WebDriverActions($table->driver);
                $action->moveToElement($elemLi)->click()->perform();
                $table->pause(2000);
            }
        });
        $browser->with('#table-schedule-result', function ($table) {
            $table->assertSee('車両No')
                ->assertSee('月');
        });
    }

    //case download PDF
    public function caseDownloadPDF($browser)
    {
        $browser->pause(2000)->click('.btn-schedule-result')->pause(4000);
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
        $browser->assertSee('出力')->click('maintenance-pdf-btn')->pause(5000);
    }
}


