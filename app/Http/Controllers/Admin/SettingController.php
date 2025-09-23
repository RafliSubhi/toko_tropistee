<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'store_latitude' => ['nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'store_longitude' => ['nullable', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'shipping_rate_per_km' => 'nullable|numeric|min:0',
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'dana_link' => 'nullable|url',
            'gopay_link' => 'nullable|url',
            'ovo_link' => 'nullable|url',
            'google_maps_embed_url' => 'nullable|url',
            'dana_qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gopay_qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ovo_qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle image uploads
        $imageFields = ['qris_image', 'dana_qr_code', 'gopay_qr_code', 'ovo_qr_code'];
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $oldPath = Setting::where('key', $field)->first()->value ?? null;
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file($field)->store('qris', 'public');
                Setting::updateOrCreate(['key' => $field], ['value' => $path]);
            }
        }

        // Update text-based settings
        $textKeys = [
            'contact_email',
            'phone_number',
            'address',
            'google_maps_embed_url',
            'store_latitude',
            'store_longitude',
            'shipping_rate_per_km',
            'dana_link',
            'gopay_link',
            'ovo_link'
        ];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key) ?? '']);
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function saldo()
    {
        $totalBalanceSetting = Setting::firstOrCreate(['key' => 'total_balance'], ['value' => '0']);
        return view('admin.settings.saldo', ['totalBalance' => $totalBalanceSetting->value]);
    }

    public function updateSaldo(Request $request)
    {
        $validated = $request->validate([
            'total_balance' => 'required|numeric|min:0',
        ]);

        Setting::updateOrCreate(
            ['key' => 'total_balance'],
            ['value' => $validated['total_balance']]
        );

        return redirect()->route('admin.settings.saldo')->with('success', 'Total saldo berhasil diperbarui.');
    }
}