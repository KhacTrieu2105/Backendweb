<?php

namespace Database\Seeders;

use App\Models\StockIn;
use App\Models\Product;
use Illuminate\Database\Seeder;

class StockInSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all(); // Lấy hết sản phẩm hiện có

        if ($products->count() === 0) {
            $this->command->error('Không có sản phẩm nào! Hãy tạo sản phẩm trước.');
            return;
        }

        // Nếu chỉ có ít sản phẩm thì dùng hết, không random quá số lượng có
        $availableCount = $products->count();

        for ($i = 1; $i <= 20; $i++) {
            $items = [];
            $total = 0;

            // Lấy số lượng sản phẩm ngẫu nhiên nhưng không vượt quá số có thật
            $take = min(rand(1, 5), $availableCount);
            $selectedProducts = $products->random($take);

            foreach ($selectedProducts as $product) {
                $qty = rand(10, 200);
                $unitPrice = $product->price * 0.75;
                $amount = $qty * $unitPrice;

                $items[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'quantity'     => $qty,
                    'unit_price'   => round($unitPrice),
                    'amount'       => $amount,
                ];

                $total += $amount;
            }

            StockIn::create([
                'code'         => 'NK' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'supplier_id'  => null,
                'total_amount' => $total,
                'note'         => 'Nhập kho tự động - phiếu số ' . $i,
                'status'       => 1,
                'created_by'   => 1,
                'updated_by'   => 1,
                'created_at'   => now()->subDays(rand(0, 90)),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info("Tạo thành công 20 phiếu nhập kho với {$availableCount} sản phẩm hiện có!");
    }
}