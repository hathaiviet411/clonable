<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;
class SchedulePriodicTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSchedulePriodic() {
        $this->browse(function (Browser $browser) {
            $this->login($browser, "111111", "123456789", "整備予定実施表"); //correct
            $this->visitByClick($browser, ".router-item-0", '.maintenance-schedule-results', '整備予定実施表');
            $this->changeDepartment($browser);
            for ($i = 1; $i <= 3; $i++) {
                $this->randExportPdf($browser);
            }
            $this->filter($browser);
        });
    }

    private function changeDepartment($browser) {
        $browser->click('#department-selector__BV_toggle_')->pause(1000);
            $count = 0;
            $selectDepartments = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="department-selector"]/ul/li'));
            for ($i = count($selectDepartments) - 1; $i >= 0; $i--) {
                $selectDepartments[$i]->click();
                $browser->pause(100);
                $browser->assertSee('読み込み中');
                $browser->waitUntilMissing('.b-icon-animation-spin')->pause(100);
                $browser->assertMissing(".toast-body");
                $browser->pause(2000);
                $this->changeYear($browser);
                $browser->click('#department-selector__BV_toggle_')->pause(2000);
                $count++;
                if ($count == 5) break;
            }
    }

    private function changeYear($browser) {
        $browser->click('#date-time-selector__BV_toggle_')->pause(1000);
        $selectYears= $browser->driver->findElements(WebDriverBy::xpath('//*[@id="date-time-selector"]/ul/li'));
        $count = 0;
            for ($i = count($selectYears) - 1; $i >= 0; $i--) {
                $selectYears[$i]->click();
                $browser->pause(100);
                $browser->assertSee('読み込み中');
                $browser->waitUntilMissing('.b-icon-animation-spin')->pause(100);
                $browser->assertMissing(".toast-body");
                $browser->click('#date-time-selector__BV_toggle_')->pause(1000);
                $count++;
                if ($count == 5) break;
            }
    }

    private function randExportPdf($browser) {
        $browser->click('#department-selector__BV_toggle_')->pause(1000);
        $selectDepartments = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="department-selector"]/ul/li'));
        $browser->pause(200);
        $browser->click('#date-time-selector__BV_toggle_')->pause(1000);
        $selectYears= $browser->driver->findElements(WebDriverBy::xpath('//*[@id="date-time-selector"]/ul/li'));
        $browser->pause(200);
        $indexDp = rand(0, count($selectDepartments) - 1);
        $indexYear = rand(0, count($selectYears));

        $selectYears[$indexYear]->click();
        $browser->pause(100);
        $browser->assertSee('読み込み中');
        $browser->waitUntilMissing('.b-icon-animation-spin');
        $browser->assertMissing(".toast-body");

        $browser->click('#department-selector__BV_toggle_')->pause(1000);
        $selectDepartments[$indexDp]->click();
        $browser->pause(100);
        $browser->assertSee('読み込み中');
        $browser->waitUntilMissing('.b-icon-animation-spin');
        $browser->assertMissing(".toast-body");

        $browser->press('.maintenance-pdf-btn');
        $browser->pause(2000);
    }

    private function filter($browser) {
        $browser->press('.filter-title')->pause(500);
        $browser->check('.chk_filter')->pause(500); // open filter

        $browser->press('#filter-by-number-plate');
        $selectTruck = $browser->driver->findElements(WebDriverBy::xpath('//*[@id="filter-by-number-plate"]/option'));
        for ($i = 0; $i <  2; $i++) {
            $indexTruck = rand(0, count($selectTruck) - 1);
            $selectTruck[$indexTruck]->click();
            $browser->pause(1000);
            $browser->click('.btn-summit-filter');
            $browser->pause(2000);
            $browser->press('.maintenance-pdf-btn');
        }
        $browser->pause(2000);
        $selectTruck[0]->click();
        $browser->pause(1000);
        $browser->click('.btn-summit-filter');
        $browser->pause(2000);
    }

    private function unFilter($browser) {
        $browser->check('.chk_filter')->pause(500); // open filter
        $browser->pause(1000);
        $browser->click('.btn-summit-filter');
        $browser->pause(1000);
    }
}
