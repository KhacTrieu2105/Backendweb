<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Lấy danh sách menu
     */
    public function index()
    {
        $menus = Menu::orderBy('sort_order')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách menu thành công',
            'data'    => $menus
        ]);
    }

    /**
     * Tạo menu mới
     */
    public function store(Request $request)
    {
        $menu = Menu::create([
            'name'       => $request->name,
            'link'       => $request->link,
            'type'       => $request->type,
            'parent_id'  => $request->parent_id ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'table_id'   => $request->table_id ?? 0,
            'position'   => $request->position ?? 'main',
            'created_at' => now(),
            'created_by' => 1,
            'status'     => 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Tạo menu thành công',
            'data'    => $menu
        ], 201);
    }

    /**
     * Lấy chi tiết menu theo ID
     */
    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status'  => false,
                'message' => 'Menu không tồn tại'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $menu
        ]);
    }

    /**
     * Cập nhật menu
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status'  => false,
                'message' => 'Menu không tồn tại'
            ], 404);
        }

        $menu->update([
            'name'       => $request->name ?? $menu->name,
            'link'       => $request->link ?? $menu->link,
            'type'       => $request->type ?? $menu->type,
            'parent_id'  => $request->parent_id ?? $menu->parent_id,
            'sort_order' => $request->sort_order ?? $menu->sort_order,
            'table_id'   => $request->table_id ?? $menu->table_id,
            'position'   => $request->position ?? $menu->position,
            'updated_at' => now(),
            'updated_by' => 1,
            'status'     => $request->status ?? $menu->status,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật menu thành công',
            'data'    => $menu
        ]);
    }

    /**
     * Xóa menu
     */
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'status'  => false,
                'message' => 'Menu không tồn tại'
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Xóa menu thành công'
        ]);
    }
}
