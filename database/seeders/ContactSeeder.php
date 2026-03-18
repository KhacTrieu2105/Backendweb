<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    public function run()
    {
        DB::table('contacts')->insert([
            [
                'user_id' => 1,
                'name' => 'Nguyen Van A',
                'email' => 'a@example.com',
                'phone' => '0909123456',
                'content' => 'Liên hệ thử nghiệm 1',
                'reply_id' => 0,
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'user_id' => null,
                'name' => 'Nguyen Van B',
                'email' => 'b@example.com',
                'phone' => '0909876543',
                'content' => 'Liên hệ thử nghiệm 2',
                'reply_id' => 0,
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
        ]);
    }
}
