<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // By default, show all orders relevant to production
            $query->whereIn('status', ['accepted', 'processing']);
        }

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
}