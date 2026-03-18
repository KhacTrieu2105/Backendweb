<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $limit  = $request->input('limit', 10);
        $page   = $request->input('page', 1);
        $search = $request->input('search');

        $query = Promotion::with('product')->where('status', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%$search%");
                  });
            });
        }

        if ($request->get('active') === 'now') {
            $query->where('date_begin', '<=', now())
                  ->where('date_end', '>=', now());
        }

        $total = $query->count();

        $promotions = $query
            ->orderBy('date_begin', 'desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

      $promotions->each(function ($promo) {
    if ($promo->product) {
        $original = $promo->product->price_buy ?? $promo->product->price;
        $sale     = $promo->price_sale;

        $promo->original_price   = (int) $original;
        $promo->sale_price       = (int) $sale;
        $promo->discount_percent = $original > 0
            ? round((($original - $sale) / $original) * 100)
            : 0;

        $promo->product_name = $promo->product->name;
        $promo->product_slug = $promo->product->slug ?? '';

        // ✅ FIX CHUẨN Ở ĐÂY
        $promo->image = $promo->product->image_url;
    
    }

    unset($promo->product);
});


        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách khuyến mãi thành công',
            'data' => $promotions,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'current_page' => (int) $page,
                'last_page' => ceil($total / $limit),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'product_id'  => 'required|exists:products,id',
            'price_sale'  => 'required|numeric|min:0',
            'date_begin'  => 'required|date',
            'date_end'    => 'required|date|after_or_equal:date_begin',
            'status'      => 'required|boolean',
        ]);

        $promo = Promotion::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Tạo khuyến mãi thành công',
            'data' => $promo
        ]);
    }

    public function destroy($id)
    {
        $promo = Promotion::find($id);

        if (!$promo) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khuyến mãi'
            ], 404);
        }

        $promo->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa khuyến mãi'
        ]);
    }
}
