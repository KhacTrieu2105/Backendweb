<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // ======================
    // LIST
    // ======================
   public function index(Request $request)
{
    $query = Banner::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('position')) {
        $query->where('position', $request->position);
    }

    // Model Banner đã có $appends = ['image_url'] nên ở đây không cần map thủ công nữa
    $banners = $query->orderBy('created_at', 'desc')->get();

    return response()->json([
        'status' => true,
        'data' => $banners
    ]);
}
    // ======================
    // STORE
    // ======================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required',
            'position' => 'required'
        ]);

        $banner = Banner::create([
            'name' => $request->name,
            'image' => $request->image,
            'link' => $request->link,
            'position' => $request->position,
            'status' => 1,
            'created_by' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm banner thành công',
            'data' => $banner
        ], 201);
    }

    // ======================
    // SHOW
    // ======================
   public function show($id)
{
    // Sử dụng find để lấy bản ghi
    $banner = Banner::find($id);

    if (!$banner) {
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy banner'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $banner // Đảm bảo trả về object banner nằm trong key 'data'
    ]);
}

    // ======================
    // UPDATE
    // ======================
    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy banner'
            ], 404);
        }

        $banner->update([
            'name' => $request->name ?? $banner->name,
            'image' => $request->image ?? $banner->image,
            'link' => $request->link ?? $banner->link,
            'position' => $request->position ?? $banner->position,
            'status' => $request->status ?? $banner->status,
            'updated_by' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật banner thành công',
            'data' => $banner
        ]);
    }

    // ======================
    // DELETE
    // ======================
    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy banner'
            ], 404);
        }

        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa banner thành công'
        ]);
    }
}
