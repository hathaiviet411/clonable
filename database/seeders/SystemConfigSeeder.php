<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use Illuminate\Database\Seeder;
use App\Models\Department;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!SystemConfig::where('sys_param', 'start_year')->first()) {
            SystemConfig::create([
                'sys_param' => 'start_year',
                'sys_value' => '2022'
            ]);
        }
        if (!SystemConfig::where('sys_param', 'next_year')->first()) {
            SystemConfig::create([
                'sys_param' => 'next_year',
                'sys_value' => '2028'
            ]);
        }
    }
}


