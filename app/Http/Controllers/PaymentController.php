<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\PreventBrowserCaching::class);
    }
    public function show(Order $order)
    {
        // Pastikan hanya pemilik pesanan yang bisa melihat halaman ini
        if ($order->customer_email !== auth()->guard('pengunjung')->user()->email) {
            abort(403);
        }

        // Pastikan statusnya benar
        if ($order->status !== 'menunggu pembayaran') {
            return redirect()->route('pesanan-saya.index')->with('info', 'Pesanan ini tidak lagi memerlukan pembayaran.');
        }

        $settings = Setting::pluck('value', 'key');
        $paymentMethod = $order->payment_method; // DANA, GOPAY, OVO
        $qrCodeUrl = $settings['qr_' . strtolower($paymentMethod) . '_url'] ?? null;

        return view('pembayaran.show', compact('order', 'qrCodeUrl', 'paymentMethod'));
    }

    public function confirm(Request $request, Order $order)
    {
        if ($order->customer_email !== auth()->guard('pengunjung')->user()->email) {
            abort(403);
        }

        $order->update(['status' => 'belum diterima']);

        return redirect()->route('pesanan-saya.index')->with('success', 'Terima kasih! Pembayaran Anda akan segera kami verifikasi.');
    }
}
