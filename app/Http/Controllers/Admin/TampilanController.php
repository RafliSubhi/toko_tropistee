<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Setting;

class TampilanController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        $logo = \App\Models\Logo::first();
        return view('admin.tampilan.index', compact('settings', 'logo'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp|max:51200',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,ico|max:51200',
        ]);

        // Handle file uploads
        $logoModel = \App\Models\Logo::firstOrCreate([]);

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($logoModel->logo_path && Storage::disk('public')->exists($logoModel->logo_path)) {
                Storage::disk('public')->delete($logoModel->logo_path);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $logoModel->logo_path = $logoPath;
        }

        if ($request->hasFile('favicon')) {
            // Delete old favicon if it exists
            if ($logoModel->favicon_path && Storage::disk('public')->exists($logoModel->favicon_path)) {
                Storage::disk('public')->delete($logoModel->favicon_path);
            }
            $faviconPath = $request->file('favicon')->store('favicons', 'public');
            $logoModel->favicon_path = $faviconPath;
        }

        $logoModel->save();
        
        // Handle other settings
        $data = $request->except(['_token', 'logo', 'favicon']);
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('public/settings');
                $value = str_replace('public/', '', $path);
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.tampilan.index')->with('success', 'Pengaturan tampilan berhasil diperbarui.');
    }
}
