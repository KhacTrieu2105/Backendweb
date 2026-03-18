<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockInController extends Controller
{
    // ================= DANH SÁCH =================
 // ================= DANH SÁCH =================
public function index(Request $request)
{
    // Sử dụng withSum để tính tổng cột 'quantity' từ bảng stock_in_items
    $query = StockIn::withSum('items as total_qty', 'quantity'); 

    if ($request->filled('search')) {
        $query->where('code', 'like', '%' . $request->search . '%')
              ->orWhere('note', 'like', '%' . $request->search . '%');
    }

    $limit = $request->input('limit', 10);
    $data = $query->orderBy('id', 'desc')->paginate($limit);

    return response()->json([
        'status' => true,
        'data' => $data->items(), // Lúc này trong mỗi item sẽ có thêm trường total_qty
        'total' => $data->total(),
    ]);
}

    // ================= CHI TIẾT =================
    public function show($id)
    {
        $stockIn = StockIn::with(['items.product'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $stockIn
        ]);
    }

    // ================= THÊM MỚI =================
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer',
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Sinh mã phiếu nhập an toàn
            $code = 'NK' . now()->format('YmdHis');

            $stockIn = StockIn::create([
                'code' => $code,
                'supplier_id' => $request->supplier_id,
                'note' => $request->note,
                'total_amount' => 0,
                'status' => 1,
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]);

            $totalAmount = 0;

            foreach ($request->products as $item) {
                // lưu chi tiết nhập kho
                StockInItem::create([
                    'stock_in_id' => $stockIn->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // cộng tồn kho
                Product::where('id', $item['product_id'])
                    ->increment('qty', $item['quantity']);

                $totalAmount += $item['quantity'] * $item['price'];
            }

            // cập nhật tổng tiền
            $stockIn->update([
                'total_amount' => $totalAmount,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Nhập kho thành công',
                'data' => $stockIn->load('items.product')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // \Log::error('StockIn error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
