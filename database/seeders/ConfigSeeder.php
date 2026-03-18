<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    public function run()
    {
        DB::table('configs')->insert([
            [
                'site_name'=>'Website Bán Túi Sách',
                'email'=>'info@example.com',
                'phone'=>'0909000000',
                'hotline'=>'19001234',
                'address'=>'Hà Nội, Việt Nam',
                'status'=>1
            ]
        ]);
    }
}
