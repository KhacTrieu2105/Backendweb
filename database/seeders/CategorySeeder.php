<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            [
                'name' => 'Túi xách nữ',
                'slug' => 'tui-xach-nu',
                'image' => 'bag_women.jpg',
                'parent_id' => 0,
                'sort_order' => 1,
                'description' => 'Danh mục túi xách nữ',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Túi xách nam',
                'slug' => 'tui-xach-nam',
                'image' => 'bag_men.jpg',
                'parent_id' => 0,
                'sort_order' => 2,
                'description' => 'Danh mục túi xách nam',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],

            // ==========================
            // 🚀 10 danh mục bổ sung
            // ==========================

            [
                'name' => 'Túi đeo chéo',
                'slug' => 'tui-deo-cheo',
                'image' => 'crossbag.jpg',
                'parent_id' => 0,
                'sort_order' => 3,
                'description' => 'Danh mục túi đeo chéo cho nam và nữ',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Túi tote',
                'slug' => 'tui-tote',
                'image' => 'tote.jpg',
                'parent_id' => 0,
                'sort_order' => 4,
                'description' => 'Danh mục túi tote thời trang',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Ba lô nữ',
                'slug' => 'balo-nu',
                'image' => 'backpack_women.jpg',
                'parent_id' => 0,
                'sort_order' => 5,
                'description' => 'Danh mục balo dành cho nữ',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Ba lô nam',
                'slug' => 'balo-nam',
                'image' => 'backpack_men.jpg',
                'parent_id' => 0,
                'sort_order' => 6,
                'description' => 'Danh mục balo dành cho nam',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Túi du lịch',
                'slug' => 'tui-du-lich',
                'image' => 'travelbag.jpg',
                'parent_id' => 0,
                'sort_order' => 7,
                'description' => 'Danh mục túi du lịch tiện dụng',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Túi vải canvas',
                'slug' => 'tui-vai-canvas',
                'image' => 'canvasbag.jpg',
                'parent_id' => 0,
                'sort_order' => 8,
                'description' => 'Danh mục túi canvas tự nhiên',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Ví nữ',
                'slug' => 'vi-nu',
                'image' => 'wallet_women.jpg',
                'parent_id' => 0,
                'sort_order' => 9,
                'description' => 'Danh mục ví nữ cao cấp',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Ví nam',
                'slug' => 'vi-nam',
                'image' => 'wallet_men.jpg',
                'parent_id' => 0,
                'sort_order' => 10,
                'description' => 'Danh mục ví nam da thật',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Phụ kiện túi xách',
                'slug' => 'phu-kien-tui-xach',
                'image' => 'bag_accessories.jpg',
                'parent_id' => 0,
                'sort_order' => 11,
                'description' => 'Danh mục phụ kiện cho túi xách',
                'created_at' => now(),
                'created_by' => 1,
                'status' => 1,
            ],
        ]);
    }
}
