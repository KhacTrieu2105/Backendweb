<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStoreSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_stores')->insert([
            ['product_id'=>1,'price_root'=>500000,'qty'=>50,'created_at'=>now(),'created_by'=>1,'status'=>1],
            ['product_id'=>2,'price_root'=>600000,'qty'=>30,'created_at'=>now(),'created_by'=>1,'status'=>1],
        ]);
    }
}
