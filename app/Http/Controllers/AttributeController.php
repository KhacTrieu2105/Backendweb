<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    /**
     * Lấy danh sách tất cả attributes
     */
   public function index()
{
    // Lấy tất cả thuộc tính
    $attributes = Attribute::orderBy('id', 'asc')->get();

    return response()->json([
        'status' => true,
        'data' => $attributes, // Gói mảng vào trong key 'data'
        'message' => 'Lấy danh sách thành công'
    ], 200);
}
    /**
     * Thêm attribute mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $attribute = Attribute::create([
            'name' => $request->name,
            'status' => $request->status ?? 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm thuộc tính thành công',
            'data' => $attribute
        ], 201);
    }

    /**
     * Cập nhật attribute
     */
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $attribute->update([
            'name' => $request->name,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công'
        ]);
    }

    /**
     * Xóa attribute
     */
    public function destroy($id)
    {
        Attribute::destroy($id);

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công'
        ]);
    }
    // File: AttributeController.php
public function show($id)
{
    $attribute = Attribute::find($id);
    if (!$attribute) {
        return response()->json(['message' => 'Không tìm thấy'], 404);
    }
    return response()->json($attribute);
}
}