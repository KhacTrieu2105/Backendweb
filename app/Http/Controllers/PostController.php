<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($request->filled('post_type')) {
            $query->where('post_type', $request->post_type);
        }

        $limit = $request->input('limit', 10);
        $page  = $request->input('page', 1);

        $total = $query->count();

        $posts = $query
            ->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        // Format data để đảm bảo image_url được include
        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'topic_id' => $post->topic_id,
                'title' => $post->title,
                'slug' => $post->slug,
                'image' => $post->image,
                'image_url' => $post->image_url, // Explicitly include image_url
                'content' => $post->content,
                'description' => $post->description,
                'post_type' => $post->post_type,
                'status' => $post->status,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'created_by' => $post->created_by,
                'updated_by' => $post->updated_by,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $formattedPosts,
            'total'  => $total
        ]);
    }

    public function store(Request $request)
    {
        // Debug logging
        Log::info('Post Store Request Debug', [
            'all_data' => $request->all(),
            'has_file_image' => $request->hasFile('image'),
            'files' => $request->allFiles(),
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
        ]);

        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'title'    => 'required|string|max:255',
            'content'  => 'required',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Add image validation
        ]);

        $post = new Post();
        $post->topic_id    = $request->topic_id;
        $post->title       = $request->title;
        $post->slug        = Str::slug($request->title);
        $post->content     = $request->content;
        $post->description = $request->description ?? '';
        $post->post_type   = $request->post_type ?? 'post';
        $post->status      = $request->status ?? 1;
        $post->created_by  = 1;
        $post->created_at  = now();
        $post->image       = ''; // Default empty string

        // Xử lý upload ảnh - cho phép lưu vào nhiều thư mục như products
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Tạo tên file unique
            $fileName = time() . '_' . uniqid() . '.' . $extension;

            // Lưu vào thư mục posts (có thể thay đổi thành products hoặc khác)
            $imagePath = $file->storeAs('posts', $fileName, 'public');

            Log::info('Image uploaded successfully', [
                'original_name' => $originalName,
                'stored_path' => $imagePath,
                'file_size' => $file->getSize(),
            ]);

            $post->image = $imagePath;
        } else {
            Log::info('No image file detected in request');
        }

        $post->save();

        Log::info('Post saved with image', ['post_id' => $post->id, 'image_path' => $post->image]);

        // Format response data để include image_url
        $formattedPost = [
            'id' => $post->id,
            'topic_id' => $post->topic_id,
            'title' => $post->title,
            'slug' => $post->slug,
            'image' => $post->image,
            'image_url' => $post->image_url,
            'content' => $post->content,
            'description' => $post->description,
            'post_type' => $post->post_type,
            'status' => $post->status,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'created_by' => $post->created_by,
            'updated_by' => $post->updated_by,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Tạo bài viết thành công',
            'data'    => $formattedPost
        ], 201);
    }

    public function show($id)
{
    // Thử tìm theo ID trước, nếu không được thì tìm theo Slug
    $post = Post::where('id', $id)
                ->orWhere('slug', $id)
                ->first();

    if (!$post) {
        return response()->json([
            'status' => false,
            'message' => 'Không tìm thấy bài viết'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'data' => $post
    ]);
}

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => false], 404);
        }

        // Debug logging
        Log::info('Post Update Request Debug', [
            'post_id' => $id,
            'all_data' => $request->all(),
            'has_file_image' => $request->hasFile('image'),
            'files' => $request->allFiles(),
            'content_type' => $request->header('Content-Type'),
        ]);

        // Validation
        $request->validate([
            'topic_id' => 'sometimes|required|exists:topics,id',
            'title'    => 'sometimes|required|string|max:255',
            'content'  => 'sometimes|required',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Add image validation
        ]);

        // Chuẩn bị dữ liệu cập nhật
        $updateData = [
            'topic_id'    => $request->topic_id ?? $post->topic_id,
            'title'       => $request->title ?? $post->title,
            'content'     => $request->content ?? $post->content,
            'description' => $request->description ?? $post->description,
            'status'      => $request->status ?? $post->status,
            'updated_by'  => 1,
            'updated_at'  => now(),
        ];

        // Tạo slug nếu có title mới
        if ($request->title) {
            $updateData['slug'] = Str::slug($request->title);
        }

        // Xử lý upload ảnh mới
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Tạo tên file unique
            $fileName = time() . '_' . uniqid() . '.' . $extension;

            // Xóa ảnh cũ nếu có
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
                Log::info('Deleted old image', ['old_path' => $post->image]);
            }

            // Lưu ảnh mới
            $imagePath = $file->storeAs('posts', $fileName, 'public');
            $updateData['image'] = $imagePath;

            Log::info('New image uploaded for post update', [
                'post_id' => $id,
                'original_name' => $originalName,
                'stored_path' => $imagePath,
            ]);
        } else {
            Log::info('No new image uploaded for post update', ['post_id' => $id]);
        }

        $post->update($updateData);

        // Format response data để include image_url
        $updatedPost = Post::find($id); // Re-fetch để đảm bảo data mới nhất
        $formattedPost = [
            'id' => $updatedPost->id,
            'topic_id' => $updatedPost->topic_id,
            'title' => $updatedPost->title,
            'slug' => $updatedPost->slug,
            'image' => $updatedPost->image,
            'image_url' => $updatedPost->image_url,
            'content' => $updatedPost->content,
            'description' => $updatedPost->description,
            'post_type' => $updatedPost->post_type,
            'status' => $updatedPost->status,
            'created_at' => $updatedPost->created_at,
            'updated_at' => $updatedPost->updated_at,
            'created_by' => $updatedPost->created_by,
            'updated_by' => $updatedPost->updated_by,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thành công',
            'data'    => $formattedPost
        ]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => false], 404);
        }

        $post->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Đã xóa bài viết'
        ]);
    }
    public function showBySlug($slug)
{
    $post = Post::where('slug', $slug)->first();

    if (!$post) {
        // Nếu không tìm thấy slug, thử tìm theo ID (phòng hờ)
        $post = Post::find($slug);
    }

    if (!$post) {
        return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
    }

    // Trình bày dữ liệu giống như hàm show của bạn...
    return response()->json([
        'status' => true,
        'data'   => $post // Đảm bảo include image_url như bạn đã làm
    ]);
}
}
