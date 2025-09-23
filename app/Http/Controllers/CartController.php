<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $pengunjung = Auth::guard('pengunjung')->user();

        $cartItem = Cart::where('pengunjung_id', $pengunjung->id)
                        ->where('product_id', $product->id)
                        ->first();

        if ($cartItem) {
            // Jika produk sudah ada di keranjang, jangan tambahkan lagi
            return redirect()->route('pengunjung.cart.index')->with('warning', 'Produk sudah ada di dalam keranjang!');
        } else {
            // Jika belum ada, buat entri baru
            Cart::create([
                'pengunjung_id' => $pengunjung->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('pengunjung.cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function index()
    {
        $cartItems = Cart::where('pengunjung_id', Auth::guard('pengunjung')->id())->with('product')->get();
        $logo = \App\Models\Logo::first();
        $settings = \App\Models\Setting::pluck('value', 'key');

        $unreadNotifications = collect();
        $cartCount = 0;
        $activeOrderCount = 0;

        if (Auth::guard('pengunjung')->check()) {
            $user = Auth::guard('pengunjung')->user();
            $unreadNotifications = \App\Models\UserNotification::where('user_id', $user->id)->where('is_read', false)->latest()->get();
            $cartCount = \App\Models\Cart::where('pengunjung_id', $user->id)->count();
            $activeOrderCount = \App\Models\Order::where('pengunjung_id', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->count();
        }

        return view('keranjang.index', compact('cartItems', 'logo', 'settings', 'unreadNotifications', 'cartCount', 'activeOrderCount'));
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cart->update(['quantity' => $request->quantity]);
        return redirect()->route('pengunjung.cart.index')->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return redirect()->route('pengunjung.cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }
}
