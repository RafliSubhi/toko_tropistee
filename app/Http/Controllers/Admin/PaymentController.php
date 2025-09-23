<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $orders = Order::where('payment_status', 'waiting_confirmation')->latest()->paginate(15);
        return view('admin.payment.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|string|in:paid,failed',
        ]);

        if ($order->payment_status !== 'waiting_confirmation') {
            return back()->with('error', 'Status pembayaran untuk pesanan ini tidak dapat diubah.');
        }

        $order->update(['payment_status' => $request->payment_status]);

        // If payment is successful (paid), move the order to processing, unless it's already further along
        if ($request->payment_status === 'paid' && $order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        return redirect()->route('admin.payment.index')->with('success', 'Status pembayaran untuk Pesanan #' . $order->id . ' telah diperbarui.');
    }
}
