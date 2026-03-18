<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        DB::table('attributes')->insert([
            ['name'=>'Màu sắc'],
            ['name'=>'Kích thước'],
            ['name'=>'Chất liệu'],
        ]);
    }
}
