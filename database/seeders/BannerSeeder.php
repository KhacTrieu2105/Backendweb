<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run()
    {
        DB::table('banners')->insert([
            [
                'name' => 'Banner Slideshow 1',
                'image' => null, // Để null để sử dụng placeholder
                'link' => '#',
                'position' => 'slideshow',
                'sort_order' => 1,
                'description' => 'Banner slideshow mẫu 1',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Banner Slideshow 2',
                'image' => null,
                'link' => '#',
                'position' => 'slideshow',
                'sort_order' => 2,
                'description' => 'Banner slideshow mẫu 2',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Banner Ads 1',
                'image' => null,
                'link' => '#',
                'position' => 'ads',
                'sort_order' => 3,
                'description' => 'Banner ads mẫu 1',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Banner Ads 2',
                'image' => null,
                'link' => '#',
                'position' => 'ads',
                'sort_order' => 4,
                'description' => 'Banner ads mẫu 2',
                'status' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
