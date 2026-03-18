<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    // LIST
    public function index(Request $request)
    {
        $query = Topic::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $topics = $query->orderBy('sort_order')->get();

        return response()->json([
            'status' => true,
            'data' => $topics
        ]);
    }

    // SHOW
    public function show($id)
    {
        $topic = Topic::find($id);

        if (!$topic) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy chủ đề'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $topic
        ]);
    }

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $topic = Topic::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? 0,
            'description' => $request->description,
            'status' => 1,
            'created_at' => now(),
            'created_by' => 1,
        ]);

        return response()->json([
            'status' => true,
            'data' => $topic,
            'message' => 'Thêm chủ đề thành công'
        ], 201);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $topic = Topic::find($id);
        if (!$topic) {
            return response()->json(['status' => false], 404);
        }

        $topic->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
            'updated_at' => now(),
            'updated_by' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công'
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        Topic::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công'
        ]);
    }
}
