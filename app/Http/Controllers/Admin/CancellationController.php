<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Models\CancellationRequest;

class CancellationController extends Controller
{
    public function index(Request $request)
    {
        $query = CancellationRequest::query();

        if ($request->filled('search')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->input('search') . '%');
            });
        }

        $cancellationRequests = $query->where('status', 'pending')
                                                    ->with('order.products')
                                                    ->latest()
                                                    ->get();

        return view('admin.cancellations.index', compact('cancellationRequests'));
    }

    public function approve(Order $order)
    {
        $cancellationRequest = $order->cancellationRequest;

        if (!$cancellationRequest || $cancellationRequest->status !== 'pending') {
            return redirect()->route('admin.cancellations.index')->with('error', 'Permintaan pembatalan tidak valid atau sudah diproses.');
        }

        $cancellationRequest->update(['status' => 'approved']);
        $order->update(['status' => 'cancelled']);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => 'Permintaan pembatalan untuk pesanan #' . $order->id . ' telah disetujui.',
            'link' => route('pengunjung.pesanan-saya.history'),
        ]);

        return redirect()->route('admin.cancellations.index')->with('success', 'Permintaan pembatalan untuk pesanan #' . $order->id . ' telah disetujui.');
    }

    public function reject(Order $order)
    {
        $cancellationRequest = $order->cancellationRequest;

        if (!$cancellationRequest || $cancellationRequest->status !== 'pending') {
            return redirect()->route('admin.cancellations.index')->with('error', 'Permintaan pembatalan tidak valid atau sudah diproses.');
        }

        $cancellationRequest->update(['status' => 'rejected']);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => 'Maaf, permintaan pembatalan untuk pesanan #' . $order->id . ' tidak dapat disetujui.',
            'link' => route('pengunjung.pesanan-saya.show', $order->id),
        ]);

        return redirect()->route('admin.cancellations.index')->with('success', 'Permintaan pembatalan untuk pesanan #' . $order->id . ' telah ditolak.');
    }
}
