<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('status', 1)->get();

        if ($products->count() === 0) {
            $this->command->error('Không có sản phẩm nào để tạo khuyến mãi!');
            return;
        }

        $promotions = [
            [
                'name' => 'Khuyến mãi tháng 1',
                'product_id' => $products->random()->id,
                'price_sale' => 50000,
                'date_begin' => now()->subDays(5),
                'date_end' => now()->addDays(25),
                'status' => 1,
            ],
            [
                'name' => 'Flash Sale 50%',
                'product_id' => $products->random()->id,
                'price_sale' => 75000,
                'date_begin' => now()->addDays(1),
                'date_end' => now()->addDays(7),
                'status' => 1,
            ],
            [
                'name' => 'Mùa lễ đặc biệt',
                'product_id' => $products->random()->id,
                'price_sale' => 60000,
                'date_begin' => now()->subDays(10),
                'date_end' => now()->addDays(20),
                'status' => 1,
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::create($promo);
        }

        $this->command->info('Tạo thành công ' . count($promotions) . ' khuyến mãi mẫu!');
    }
}
