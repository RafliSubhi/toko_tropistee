<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\ContactMail;

class TokoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data untuk ditampilkan di halaman depan
        $products = Product::all();
        $teamMembers = TeamMember::all();
        
        // Ambil settings dan ubah menjadi format key => value
        $settings = Setting::pluck('value', 'key');
        $logo = \App\Models\Logo::first();

        $unreadNotifications = collect();
        $cartCount = 0;
        $activeOrderCount = 0;

        if (Auth::guard('pengunjung')->check()) {
            $user = Auth::guard('pengunjung')->user();
            $unreadNotifications = \App\Models\UserNotification::where('user_id', $user->id)->where('is_read', false)->latest()->get();
            $cartCount = \App\Models\Cart::where('pengunjung_id', $user->id)->count();
            $activeOrderCount = \App\Models\Order::where('pengunjung_id', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->count();
        }

        return view('webtampil', compact('products', 'teamMembers', 'settings', 'logo', 'unreadNotifications', 'cartCount', 'activeOrderCount'));
    }

    public function menu(Request $request)
    {
        $query = $request->input('search');
        
        $products = Product::query()
            ->when($query, function ($q, $query) {
                return $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        $settings = Setting::pluck('value', 'key');
        $logo = \App\Models\Logo::first();
        $cartItems = \App\Models\Cart::where('pengunjung_id', \Illuminate\Support\Facades\Auth::guard('pengunjung')->id())->pluck('product_id')->toArray();
        
        // Menggunakan layout app karena menu dan kontak sekarang halaman terpisah
        return view('menu', compact('products', 'settings', 'query', 'logo', 'cartItems'));
    }

    public function kontak()
    {
        $settings = Setting::pluck('value', 'key');
        $logo = \App\Models\Logo::first();

        // Cek jika google_maps_url berisi iframe, lalu ekstrak src-nya
        if (isset($settings['google_maps_url']) && str_contains($settings['google_maps_url'], '<iframe')) {
            preg_match('/src="([^"]+)"/', $settings['google_maps_url'], $matches);
            $settings['google_maps_url'] = $matches[1] ?? '';
        }

        $unreadNotifications = collect();
        $cartCount = 0;
        $activeOrderCount = 0;

        if (Auth::guard('pengunjung')->check()) {
            $user = Auth::guard('pengunjung')->user();
            $unreadNotifications = \App\Models\UserNotification::where('user_id', $user->id)->where('is_read', false)->latest()->get();
            $cartCount = \App\Models\Cart::where('pengunjung_id', $user->id)->count();
            $activeOrderCount = \App\Models\Order::where('pengunjung_id', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->count();
        }

        // Menggunakan layout app karena menu dan kontak sekarang halaman terpisah
        return view('kontak', compact('settings', 'logo', 'unreadNotifications', 'cartCount', 'activeOrderCount'));
    }

    public function showMessage()
    {
        if (!session()->has('message_content')) {
            return redirect()->route('home');
        }

        return view('pesan', [
            'type' => session('message_type', 'info'),
            'message' => session('message_content', 'Terjadi kesalahan.'),
            'back_url' => session('back_url', route('home')),
        ]);
    }
}