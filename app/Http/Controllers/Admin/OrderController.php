<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->input('search') . '%');
        }

        // Filter logic based on sidebar links
        if ($request->get('filter') == 'belum_ongkir') {
            $query->where('shipping_cost', '=', 0);
        } elseif ($request->get('filter') == 'sudah_ongkir') {
            $query->where('shipping_cost', '>', 0);
        }

        $orders = $query->where('status', 'pending')->with('products')->latest()->paginate(10);
        
        return view('admin.orders.index', compact('orders'));
    }

    public function accept(Order $order)
    {
        $order->update(['status' => 'accepted']);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => 'Pesanan #' . $order->id . ' telah diterima.',
            'link' => route('pengunjung.pesanan-saya.show', $order->id),
        ]);

        return redirect()->route('admin.production.index')->with('success', 'Pesanan diterima dan dipindahkan ke Produksi.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'status' => 'sometimes|string|in:pending,processing,ready_to_ship,completed,cancelled',
        ]);

        $subtotal = $order->products->sum(function ($product) {
            return $product->pivot->price * $product->pivot->quantity;
        });

        $order->update([
            'shipping_cost' => $validated['shipping_cost'],
            'total_price' => $subtotal + $validated['shipping_cost'],
            'status' => $validated['status'],
            'ongkir_updated_at' => now(),
            'ongkir_notification_seen' => false,
        ]);

        // Create notification for user
        $message = "Status pesanan #{$order->id} Anda telah diperbarui menjadi '" . $order->indonesian_status . "'.";
        if ($order->wasChanged('shipping_cost')) {
            $message = "Ongkos kirim untuk pesanan #{$order->id} telah ditetapkan. Silakan lakukan pembayaran.";
        }

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => $message,
            'link' => route('pengunjung.pesanan-saya.show', $order->id),
        ]);

        return redirect()->route('admin.orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
