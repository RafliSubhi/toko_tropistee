<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->input('search') . '%');
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // By default, show all orders relevant to distribution
            $query->whereIn('status', ['ready_to_ship', 'shipped']);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.distribution.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:shipped,completed',
        ]);

        $newStatus = $validated['status'];

        // Logic to prevent invalid status transitions
        if (($order->status === 'ready_to_ship' && $newStatus === 'completed') || ($order->status === 'shipped' && $newStatus === 'shipped')) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $order->update(['status' => $newStatus]);

        // Create notification for the user
        $message = '';
        if ($newStatus === 'shipped') {
            $message = "Kabar baik! Pesanan #{$order->id} Anda telah dikirim.";
        } elseif ($newStatus === 'completed') {
            $message = "Pesanan #{$order->id} Anda telah sampai tujuan. Terima kasih!";
        }

        if ($message) {
            UserNotification::create([
                'user_id' => $order->pengunjung_id,
                'order_id' => $order->id,
                'message' => $message,
                'link' => route('pengunjung.pesanan-saya.show', $order->id),
            ]);
        }

        return redirect()->route('admin.distribution.index')->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui.');
    }
}
