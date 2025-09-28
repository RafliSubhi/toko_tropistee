<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->input('search') . '%');
        }

        // Filter logic based on the 'filter' parameter
        $filter = $request->input('filter');

        match ($filter) {
            'unpaid' => $query->whereIn('payment_status', ['unpaid', 'waiting_confirmation']),
            'paid' => $query->where('payment_status', 'paid')->where('status', 'accepted'),
            'processing' => $query->where('status', 'processing'),
            default => $query->whereIn('status', ['accepted', 'processing']),
        };

        $orders = $query->latest()->paginate(15)->withQueryString();
        
        return view('admin.production.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:processing,ready_to_ship',
        ]);

        if ($validated['status'] == 'ready_to_ship' && $order->status != 'processing') {
            return redirect()->back()->with('error', 'Pesanan harus diproses terlebih dahulu sebelum siap dikirim.');
        }

        $order->update(['status' => $validated['status']]);

        $message = 'Status pesanan #' . $order->id . ' telah diperbarui menjadi ' . Str::title(str_replace('_', ' ', $validated['status'])) . '.';

        if ($validated['status'] == 'ready_to_ship') {
            return redirect()->route('admin.distribution.index')->with('success', $message);
        }

        return redirect()->route('admin.production.index')->with('success', $message);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|string|in:paid,unpaid',
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        if ($validated['payment_status'] == 'paid') {
            $message = 'Pembayaran untuk pesanan #' . $order->id . ' telah dikonfirmasi.';
        } else {
            $message = 'Pembayaran untuk pesanan #' . $order->id . ' ditolak. Silakan coba lagi atau hubungi dukungan.';
        }

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => $message,
            'link' => route('pengunjung.pesanan-saya.show', $order->id),
        ]);

        return redirect()->route('admin.production.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}