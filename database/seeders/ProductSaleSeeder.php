<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSaleSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_sales')->insert([
            [
                'name' => 'Khuyến mãi túi nữ 1',
                'product_id' => 1,
                'price_sale' => 450000,
                'date_begin' => now(),
                'date_end' => now()->addDays(7),
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ]
        ]);
    }
}
