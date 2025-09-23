<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserNotification;

class CheckoutController extends Controller
{
    
    public function index()
    {
        $cartItems = Cart::where('pengunjung_id', Auth::guard('pengunjung')->id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('menu')->with('info', 'Keranjang Anda kosong. Silakan belanja dulu.');
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $settings = Setting::pluck('value', 'key');
        $logo = \App\Models\Logo::first();

        $unreadNotifications = collect();
        $cartCount = 0;
        $activeOrderCount = 0;

        if (Auth::guard('pengunjung')->check()) {
            $user = Auth::guard('pengunjung')->user();
            $unreadNotifications = UserNotification::where('user_id', $user->id)->where('is_read', false)->latest()->get();
            $cartCount = Cart::where('pengunjung_id', $user->id)->count();
            $activeOrderCount = Order::where('pengunjung_id', $user->id)->whereNotIn('status', ['cancelled', 'done'])->count();
        }

        return view('checkout.index', compact('cartItems', 'total', 'settings', 'logo', 'unreadNotifications', 'cartCount', 'activeOrderCount'));
    }

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|string|in:COD,DANA,GOPAY,OVO,QRIS',
            'shipping_cost' => 'required|numeric',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
        ]);

        $pengunjung = Auth::guard('pengunjung')->user();
        $cartItems = Cart::where('pengunjung_id', $pengunjung->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('menu')->with('error', 'Keranjang Anda kosong.');
        }

        // Re-calculate subtotal on the backend
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->product->price * $item->quantity;
        }

        $grandTotal = $subtotal + $validated['shipping_cost'];
        $order = null;

        DB::transaction(function () use ($validated, $pengunjung, $cartItems, $grandTotal, &$order) {
            // 1. Create the Order
            $order = Order::create([
                
                'pengunjung_id' => $pengunjung->id,
                'name' => $pengunjung->name,
                'email' => $pengunjung->email,
                'phone_number' => $validated['phone_number'],
                'delivery_address' => $validated['delivery_address'],
                'delivery_lat' => $validated['delivery_lat'] ?? null,
                'delivery_lng' => $validated['delivery_lng'] ?? null,
                'payment_method' => $validated['payment_method'],
                'shipping_cost' => $validated['shipping_cost'],
                'total_price' => $grandTotal,
                'status' => 'pending', // Initial status for all new orders
                'payment_status' => $validated['payment_method'] === 'COD' ? 'pending' : 'unpaid',
            ]);

            // 2. Attach products to the order
            $pivotData = [];
            foreach ($cartItems as $item) {
                $pivotData[$item->product_id] = [
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total_price' => $item->quantity * $item->product->price,
                ];
            }
            $order->products()->attach($pivotData);

            // 3. Clear the cart
            Cart::where('pengunjung_id', $pengunjung->id)->delete();
        });

        // Redirect to the order list page
        $message = 'Pesanan Anda telah diterima dan akan segera diproses.';
        return redirect()->route('pengunjung.pesanan-saya.index')->with('success', $message);
    }
}
