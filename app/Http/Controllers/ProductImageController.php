<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function index()
    {
        return response()->json(ProductImage::with('product')->get())
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

   public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|integer',
        'image'      => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
    ]);

    // Upload ảnh
    $path = $request->file('image')->store('products', 'public');

    $image = ProductImage::create([
        'product_id' => $request->product_id,
        'image'      => $path,
        'alt'        => $request->alt,
        'title'      => $request->title,
    ]);

    return response()->json([
        'status' => true,
        'data' => $image
    ], 201);
}


    public function show($id)
    {
        $image = ProductImage::with('product')->find($id);
        if (!$image) return response()->json(['message' => 'Image not found'], 404);

        return response()->json($image)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

 public function update(Request $request, $id)
{
    $image = ProductImage::find($id);
    if (!$image) {
        return response()->json(['message' => 'Not found'], 404);
    }

    if ($request->hasFile('image')) {
        // Xóa ảnh cũ
        if ($image->image) {
            Storage::disk('public')->delete($image->image);
        }

        $image->image = $request->file('image')->store('products', 'public');
    }

    $image->product_id = $request->product_id ?? $image->product_id;
    $image->alt        = $request->alt ?? $image->alt;
    $image->title      = $request->title ?? $image->title;

    $image->save();

    return response()->json([
        'status' => true,
        'data' => $image
    ]);
}

    public function destroy($id)
    {
        $image = ProductImage::find($id);
        if (!$image) return response()->json(['message' => 'Not found'], 404);

        $image->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
