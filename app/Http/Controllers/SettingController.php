<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Lấy setting
    public function index()
    {
        $setting = Setting::first();

        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => 'Chưa cấu hình website',
                'data' => null,
            ], 404);
        }

        $setting->logo_url    = $setting->logo ? asset('storage/' . $setting->logo) : null;
        $setting->favicon_url = $setting->favicon ? asset('storage/' . $setting->favicon) : null;

        return response()->json([
            'status' => true,
            'data' => $setting,
        ]);
    }

    // Cập nhật setting
    public function update(Request $request)
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = new Setting();
        }

        $setting->site_name = $request->site_name;
        $setting->email     = $request->email;
        $setting->phone     = $request->phone;
        $setting->hotline   = $request->hotline;
        $setting->address   = $request->address;

        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật cấu hình website thành công',
            'data' => $setting
        ]);
    }
}
