<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Bỏ validation attributes cũ vì giờ dùng attributes_json
        $request->validate([
            'name'        => 'required|string|max:255',
            'price_buy'   => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        DB::beginTransaction();

        try {
            $product = new Product();
            $product->name         = $request->name;
            $product->slug         = Str::slug($request->name);
            $product->category_id  = $request->category_id;
            $product->price_buy    = $request->price_buy;
            $product->description  = $request->description ?? '';
            $product->content      = $request->content ?? '';
            $product->status       = $request->status ?? 1;
            $product->created_by   = Auth::id() ?? 1;

            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('products', 'public');
                $product->thumbnail = $thumbnailPath;
            }

            $product->save();

            // ✅ LƯU THUỘC TÍNH TỪ JSON (HOẠT ĐỘNG 100% VỚI FILE UPLOAD)
            $attributesData = [];

            if ($request->has('attributes_json')) {
                $decoded = json_decode($request->attributes_json, true);
                if (is_array($decoded)) {
                    $attributesData = $decoded;
                }
            }
            // Hỗ trợ cả cách cũ nếu cần (edit sản phẩm)
            elseif ($request->has('attributes') && is_array($request->attributes)) {
                $attributesData = $request->attributes;
            }

            foreach ($attributesData as $attr) {
                $attributeId = $attr['attribute_id'] ?? null;
                $value = trim($attr['value'] ?? '');

                if ($attributeId && $value !== '') {
                    ProductAttribute::create([
                        'product_id'   => $product->id,
                        'attribute_id' => $attributeId,
                        'value'        => $value,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Thêm sản phẩm thành công',
                'data'    => $product->load('productAttributes')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Các method index, show, update, destroy giữ nguyên như cũ
    // (chỉ cần sửa phần update nếu muốn hỗ trợ attributes_json ở edit)

    public function index(Request $request)
    {
        // ... giữ nguyên code cũ của bạn
        $limit  = $request->input('limit', 10);
        $page   = $request->input('page', 1);
        $search = $request->input('search');

        $query = Product::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $total = $query->count();

        $products = $query
            ->with(['productAttributes.attribute'])
            ->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

      $productsArray = $products->map(function ($product) {
        return [
            'id'            => $product->id,
            'name'          => $product->name,
            'slug'          => $product->slug,
            'category_id'   => $product->category_id,
            'qty'           => $product->qty,
            'price_buy'     => $product->price_buy,
            'description'   => $product->description,
            'content'       => $product->content,
            'status'        => $product->status,
            'thumbnail_url' => $product->thumbnail
                ? asset('storage/' . $product->thumbnail) . '?t=' . $product->updated_at->timestamp
                : null,
            'formatted_attributes' => $product->productAttributes->map(function ($pa) {
                return [
                    'attribute_id'   => $pa->attribute_id,
                    'attribute_name' => $pa->attribute?->name ?? 'Không rõ',
                    'value'          => $pa->value ?? ''
                ];
            })->filter(fn($item) => !empty($item['value']))->values()->toArray(),
            'updated_at'    => $product->updated_at,
            'created_at'    => $product->created_at,
        ];
    });

        return response()->json([
            'status' => true,
            'data'   => $productsArray,
            'pagination' => [
                'total'        => $total,
                'limit'        => (int)$limit,
                'current_page' => (int)$page,
                'last_page'    => ceil($total / $limit),
            ],
            'timestamp' => now()->timestamp
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

    public function show($id)
    {
        $product = Product::with(['productAttributes.attribute'])->findOrFail($id);

        $product->thumbnail_url = $product->thumbnail
            ? asset('storage/' . $product->thumbnail) . '?t=' . $product->updated_at->timestamp
            : null;

        $product->formatted_attributes = $product->productAttributes->map(function ($pa) {
            return [
                'attribute_id'   => $pa->attribute_id,
                'attribute_name' => $pa->attribute?->name ?? 'Không rõ',
                'value'          => $pa->value ?? ''
            ];
        })->values()->toArray();

        return response()->json([
            'status' => true,
            'data'   => $product,
            'timestamp' => now()->timestamp
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

public function update(Request $request, $id)
{
    $product = Product::find($id);
    if (!$product) {
        return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
    }

    // Dùng $request->input() thay vì $request->filled() để đảm bảo lấy được dữ liệu từ FormData
    $product->name        = $request->input('name', $product->name);
    $product->slug        = Str::slug($request->input('name', $product->name));
    $product->category_id = $request->input('category_id', $product->category_id);
    $product->price_buy   = $request->input('price_buy', $product->price_buy);
    $product->description = $request->input('description', $product->description);
    $product->content     = $request->input('content', $product->content);
    
    // Xử lý status
    if ($request->has('status')) {
        $product->status = $request->input('status');
    }

    // Xử lý ảnh (Giữ nguyên logic của bạn nhưng thêm log để debug)
    if ($request->hasFile('thumbnail')) {
        // Xóa ảnh cũ...
        $path = $request->file('thumbnail')->store('products', 'public');
        $product->thumbnail = $path;
    }

    $product->save();

    // Xử lý Attributes (Giữ nguyên logic JSON decode của bạn)
    if ($request->has('attributes_json')) {
        $attributes = json_decode($request->attributes_json, true);
        if (is_array($attributes)) {
            ProductAttribute::where('product_id', $id)->delete();
            foreach ($attributes as $attr) {
                if (!empty($attr['value'])) {
                    ProductAttribute::create([
                        'product_id'   => $id,
                        'attribute_id' => $attr['attribute_id'],
                        'value'        => $attr['value'],
                    ]);
                }
            }
        }
    }

    return response()->json([
        'status' => true, 
        'message' => 'Cập nhật thành công',
        'data' => $product
    ]);
}
    public function getUpdatedSince(Request $request)
    {
        $since = $request->input('since'); // timestamp
        $limit = $request->input('limit', 50);

        $query = Product::query()->with(['productAttributes.attribute']);

        if ($since) {
            $query->where('updated_at', '>', date('Y-m-d H:i:s', $since));
        }

        $products = $query->orderBy('updated_at', 'desc')
                          ->limit($limit)
                          ->get();

        $productsArray = $products->map(function ($product) {
            return [
                'id'            => $product->id,
                'name'          => $product->name,
                'slug'          => $product->slug,
                'category_id'   => $product->category_id,
                'qty'           => $product->qty,
                'price_buy'     => $product->price_buy,
                'description'   => $product->description,
                'content'       => $product->content,
                'status'        => $product->status,
                'thumbnail_url' => $product->thumbnail
                    ? asset('storage/' . $product->thumbnail) . '?t=' . $product->updated_at->timestamp
                    : null,
                'formatted_attributes' => $product->productAttributes->map(function ($pa) {
                    return [
                        'attribute_id'   => $pa->attribute_id,
                        'attribute_name' => $pa->attribute?->name ?? 'Không rõ',
                        'value'          => $pa->value ?? ''
                    ];
                })->filter(fn($item) => !empty($item['value']))->values()->toArray(),
                'updated_at'    => $product->updated_at,
                'created_at'    => $product->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $productsArray,
            'timestamp' => now()->timestamp
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }

    public function destroy($id)
    {
        ProductAttribute::where('product_id', $id)->delete();
        Product::destroy($id);

        return response()->json([
            'status'  => true,
            'message' => 'Xóa thành công'
        ]);
    }
}
