<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = [
            '東京',
            '武蔵野',
            '横浜第一',
            '横浜第二',
            '横浜第三',
            '平塚',
            '平塚',
            '千葉',
            '八千代',
            '所沢',
            '古河',
            '新潟',
            '富山',
            '静岡',
            '浜松',
            '名古屋',
            '名古屋',
            '安城',
            '大阪',
            '神戸',
            '本社'
        ];
        foreach ($list as $key => $name) {
            $check = Department::where('name', $name)->first();
            if (!$check) {
                Department::create([
                    'name' => $name
                ]);
            }
        }
    }
}


