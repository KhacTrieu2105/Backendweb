<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name'=>'Admin',
                'email'=>'admin@example.com',
                'phone'=>'0909000000',
                'username'=>'admin',
                'password'=>Hash::make('123456'),
                'roles'=>'admin',
                'avatar'=>null,
                'created_at'=>now(),
                'created_by'=>1,
                'status'=>1
            ],
            [
                'name'=>'Customer',
                'email'=>'customer@example.com',
                'phone'=>'0909111111',
                'username'=>'customer',
                'password'=>Hash::make('123456'),
                'roles'=>'customer',
                'avatar'=>null,
                'created_at'=>now(),
                'created_by'=>1,
                'status'=>1
            ]
        ]);
    }
}
