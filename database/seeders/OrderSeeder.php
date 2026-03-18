<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([
            [
                'user_id'=>1,
                'name'=>'Nguyen Van A',
                'email'=>'a@example.com',
                'phone'=>'0909123456',
                'address'=>'Hà Nội',
                'note'=>'Giao nhanh',
                'total_amount'=>0,
                'payment_method'=>'cod',
                'created_at'=>now(),
                'created_by'=>1,
                'status'=>1
            ]
        ]);
    }
}
