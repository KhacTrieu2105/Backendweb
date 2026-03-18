<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_images')->insert([
            ['product_id'=>1,'image'=>'bag1_1.jpg','alt'=>'Túi nữ 1','title'=>'Túi nữ 1'],
            ['product_id'=>1,'image'=>'bag1_2.jpg','alt'=>'Túi nữ 1','title'=>'Túi nữ 1'],
            ['product_id'=>2,'image'=>'bag2_1.jpg','alt'=>'Túi nam 1','title'=>'Túi nam 1'],
        ]);
    }
}
