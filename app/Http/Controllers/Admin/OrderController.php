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
        $updateData = ['status' => 'accepted'];

        if (strtolower($order->payment_method) === 'cod') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => 'Pesanan #' . $order->id . ' telah diterima.',
            'link' => route('pengunjung.pesanan-saya.show', $order->id),
        ]);

        if (strtolower($order->payment_method) !== 'cod') {
            UserNotification::create([
                'user_id' => $order->pengunjung_id,
                'order_id' => $order->id,
                'message' => 'Bayar pesanan Anda untuk melanjutkan ke proses produksi.',
                'link' => route('pengunjung.pesanan-saya.pembayaran', $order->id),
            ]);
        }

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
        $rules = [
            'shipping_cost' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:pending,processing,ready_to_ship,completed,cancelled,done',
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone_number' => 'sometimes|required|string|max:20',
            'delivery_address' => 'sometimes|required|string',
            'payment_status' => 'sometimes|required|string|in:unpaid,paid,waiting_confirmation',
            'payment_method' => 'sometimes|required|string|max:255',
            'total_price' => 'sometimes|required|numeric|min:0',
        ];

        $validated = $request->validate($rules);

        if ($request->has('shipping_cost')) {
            $subtotal = $order->products->sum(function ($product) {
                return $product->pivot->price * $product->pivot->quantity;
            });
            $validated['total_price'] = $subtotal + $validated['shipping_cost'];
        }

        $order->update($validated);

        if ($order->wasChanged('shipping_cost')) {
            $message = "Ongkos kirim untuk pesanan #{$order->id} telah ditetapkan. Silakan lakukan pembayaran.";
            UserNotification::create([
                'user_id' => $order->pengunjung_id,
                'order_id' => $order->id,
                'message' => $message,
                'link' => route('pengunjung.pesanan-saya.show', $order->id),
            ]);
        }

        return redirect()->back()->with('success', 'Pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
