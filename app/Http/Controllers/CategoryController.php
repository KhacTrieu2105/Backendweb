<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public function index(Request $request)
{
    $query = Category::query();

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    $categories = $query->orderBy('created_at', 'desc')->get();

    return response()->json([
        'status' => true,
        'data' => $categories,
        'message' => 'Lấy danh mục thành công'
    ]);
}



public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:categories,slug',
        'image' => 'nullable|string', // TÊN FILE
        'parent_id' => 'required|integer',
        'sort_order' => 'required|integer',
        'description' => 'nullable|string',
        'status' => 'required|in:0,1',
    ]);

    $category = Category::create([
        'name' => $validated['name'],
        'slug' => $validated['slug'],
        'image' => $validated['image'] ?? null,
        'parent_id' => $validated['parent_id'],
        'sort_order' => $validated['sort_order'],
        'description' => $validated['description'] ?? '',
        'status' => $validated['status'],
        'created_by' => 1,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Tạo danh mục thành công',
        'data' => $category
    ], 201);
}

public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $request->validate([
        'name'        => 'required|string|max:255',
        'slug'        => 'required|string|max:255|unique:categories,slug,' . $id,
        'image'       => 'nullable|string|max:255',
        'parent_id'   => 'required|integer',
        'sort_order'  => 'required|integer',
        'description'=> 'nullable|string',
        'status'      => 'required|in:0,1',
    ]);

    $category->update([
        'name'        => $request->name,
        'slug'        => $request->slug,
        'image'       => $request->image,
        'parent_id'   => $request->parent_id,
        'sort_order'  => $request->sort_order,
        'description'=> $request->description ?? '',
        'status'      => $request->status,
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Cập nhật danh mục thành công',
        'data'    => $category
    ]);
}


public function destroy($id)
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy danh mục'
        ], 404);
    }

    // Xóa ảnh nếu tồn tại
    if ($category->image && file_exists(public_path("images/category/" . $category->image))) {
        unlink(public_path("images/category/" . $category->image));
    }

    $category->delete();

    return response()->json([
        'status' => true,
        'message' => 'Xóa danh mục thành công'
    ], 200);
}
public function show($id)
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy danh mục'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $category
    ], 200);
}


}
