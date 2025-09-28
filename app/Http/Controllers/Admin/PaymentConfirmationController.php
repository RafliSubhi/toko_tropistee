<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class PaymentConfirmationController extends Controller
{
    public function index()
    {
        $orders = Order::where('payment_status', 'waiting_confirmation')->latest()->paginate(10);
        return view('admin.payment_confirmation.index', compact('orders'));
    }

    public function confirm(Order $order)
    {
        $order->update(['payment_status' => 'paid']);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => 'Pembayaran untuk pesanan #' . $order->id . ' telah dikonfirmasi dan lunas.',
            'link' => route('pengunjung.pesanan-saya.show', $order->id),
        ]);

        return redirect()->route('admin.payment_confirmation.index')->with('success', 'Pembayaran untuk pesanan #' . $order->id . ' telah dikonfirmasi.');
    }

    public function reject(Order $order)
    {
        $order->update(['payment_status' => 'unpaid']);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => 'Pembayaran untuk pesanan #' . $order->id . ' belum masuk atau tidak dapat dikonfirmasi. Silakan coba lagi atau hubungi dukungan.',
            'link' => route('pengunjung.pesanan-saya.pembayaran', $order->id),
        ]);

        return redirect()->route('admin.payment_confirmation.index')->with('success', 'Pembayaran untuk pesanan #' . $order->id . ' telah ditandai sebagai belum masuk.');
    }
}
