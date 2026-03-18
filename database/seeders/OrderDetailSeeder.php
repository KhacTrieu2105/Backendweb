<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDetailSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_details')->insert([
            ['order_id'=>1,'product_id'=>1,'price'=>500000,'qty'=>2,'amount'=>1000000,'discount'=>0],
            ['order_id'=>1,'product_id'=>2,'price'=>600000,'qty'=>1,'amount'=>600000,'discount'=>50000],
        ]);
    }
}
