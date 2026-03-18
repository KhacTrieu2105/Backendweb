<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_attributes')->insert([
            ['product_id'=>1,'attribute_id'=>1,'value'=>'Đen'],
            ['product_id'=>1,'attribute_id'=>2,'value'=>'M'],
            ['product_id'=>2,'attribute_id'=>1,'value'=>'Nâu'],
            ['product_id'=>2,'attribute_id'=>3,'value'=>'Da thật'],
        ]);
    }
}
