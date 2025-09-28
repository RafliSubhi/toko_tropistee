<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;
use App\Models\Cart;
use App\Models\AdminNotification;
use App\Models\CancellationRequest;

class UserOrderController extends Controller
{
    
    // Display a list of the user's orders
    public function index()
    {
        $orders = Order::where('pengunjung_id', Auth::guard('pengunjung')->id())
                        ->whereNotIn('status', ['completed', 'cancelled', 'done'])
                        ->with('products') // Eager load products
                        ->latest()
                        ->paginate(10);

        return view('pesanan-saya.index', compact('orders'));
    }

    // Show the detail of a single order
    public function show(Order $order)
    {
        // Ensure the user can only see their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        // Eager load the cancellation request relationship
        $order->load('cancellationRequest');

        $payment_qr_code_url = null;
        // Generate QR Code URL only if it's a QRIS payment and it's unpaid
        if ($order->payment_method === 'qris' && $order->payment_status === 'unpaid') {
            $qrisData = [
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'merchant_name' => 'Toko TropisTee',
            ];
            $jsonQrisData = json_encode($qrisData);
            $payment_qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($jsonQrisData);
        }

        return view('pesanan-saya.show', compact('order', 'payment_qr_code_url'));
    }

    public function pembayaran(Order $order)
    {
        // Ensure the user can only see their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        if ($order->payment_method === 'COD') {
            return redirect()->route('pengunjung.pesanan-saya.show', $order)->with('info', 'Pesanan COD tidak memerlukan pembayaran online.');
        }

        // Fetch all settings to make them available in the view
        $settings = Setting::pluck('value', 'key');

        $qrCodePath = null;
        $paymentMethod = strtolower($order->payment_method);

        if (in_array($paymentMethod, ['dana', 'gopay', 'ovo'])) {
            $qrCodePath = $settings->get($paymentMethod . '_qr_code');
        }

        return view('pesanan-saya.pembayaran', compact('order', 'settings', 'qrCodePath'));
    }

    // Cancel an order
    public function cancel(Request $request, Order $order)
    {
        // Ensure the user can only cancel their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        // Only allow cancellation if the order is still pending
        if ($order->status_pesanan !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan karena sudah diproses.');
        }

        $order->status_pesanan = 'cancelled';
        $order->save();

        Notification::create([
            'message' => 'Pesanan #' . $order->id . ' telah dibatalkan oleh pelanggan.',
            'link' => route('admin.cancelled-orders.index'),
        ]);

        return redirect()->route('pesanan-saya.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // Confirm that payment has been made (by the user)
    public function confirmPayment(Request $request, Order $order)
    {
        // Ensure the user can only confirm their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        if ($order->payment_status !== 'unpaid') {
            return back()->with('error', 'Status pembayaran pesanan ini tidak dapat diubah.');
        }

        $request->validate([
            'nama_pengguna' => 'required|string|max:255',
        ]);

        $order->payment_status = 'waiting_confirmation';
        $order->nama_pengguna = $request->nama_pengguna;
        $order->save();

        Notification::create([
            'message' => 'Konfirmasi pembayaran untuk Pesanan #' . $order->id . ' telah diterima.',
            'link' => route('admin.payment_confirmation.index'),
        ]);

        return redirect()->route('pengunjung.pesanan-saya.index')->with('success', 'Konfirmasi pembayaran telah dikirim. Admin akan segera memverifikasi pembayaran Anda.');
    }

    // Display user's order history (completed orders)
    public function history()
    {
        $historyOrders = Order::where('pengunjung_id', Auth::guard('pengunjung')->id())
                                ->whereIn('status', ['completed', 'cancelled', 'done'])
                                ->where('is_hidden_from_history', false)
                                ->latest('updated_at')
                                ->paginate(10);

        $unreadNotifications = collect();
        $cartCount = 0;
        $activeOrderCount = 0;

        if (Auth::guard('pengunjung')->check()) {
            $user = Auth::guard('pengunjung')->user();
            $unreadNotifications = UserNotification::where('user_id', $user->id)->where('is_read', false)->latest()->get();
            $cartCount = Cart::where('pengunjung_id', $user->id)->count();
            $activeOrderCount = Order::where('pengunjung_id', $user->id)->whereNotIn('status', ['cancelled', 'done'])->count();
        }

        return view('pesanan-saya.history', compact('historyOrders', 'unreadNotifications', 'cartCount', 'activeOrderCount'));
    }

    // Hide an order from the user's history
    public function hideFromHistory(Request $request, Order $order)
    {
        // Ensure the user can only hide their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        // Ensure the order is actually completed before hiding
        if (!in_array($order->status_pesanan, ['completed', 'cancelled', 'done'])) {
            return back()->with('error', 'Hanya pesanan yang sudah selesai yang bisa disembunyikan.');
        }

        $order->is_hidden_from_history = true;
        $order->save();

        return redirect()->route('pengunjung.pesanan-saya.history')->with('success', 'Pesanan telah dihapus dari riwayat Anda.');
    }

    // Mark the shipping cost notification as seen
    public function markOngkirNotificationSeen(Order $order)
    {
        // Ensure the user can only update their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        $order->ongkir_notification_seen = true;
        $order->save();

        return redirect()->route('pesanan-saya.index');
    }

    public function showCancellationReasonForm(Order $order)
    {
        // Ensure the user can only see their own order
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        return view('pesanan-saya.cancel-reason', compact('order'));
    }

    public function requestCancellation(Request $request, Order $order)
    {
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $cancellationRequest = $order->cancellationRequest;

        // Block if a request is currently pending
        if ($cancellationRequest && $cancellationRequest->status === 'pending') {
            return redirect()->route('message.show')
                ->with('message_type', 'error')
                ->with('message_content', 'Permintaan pembatalan sebelumnya masih diproses.')
                ->with('back_url', route('pengunjung.pesanan-saya.show', $order->id));
        }

        // Block if a request was rejected within the cooldown period
        if ($cancellationRequest && $cancellationRequest->status === 'rejected') {
            $cooldownEnd = $cancellationRequest->updated_at->addMinutes(2);
            if (now()->lt($cooldownEnd)) {
                $remaining = now()->diffInMinutes($cooldownEnd) + 1;
                return redirect()->route('message.show')
                    ->with('message_type', 'error')
                    ->with('message_content', "Permintaan ditolak. Coba lagi dalam {$remaining} menit.")
                    ->with('back_url', route('pengunjung.pesanan-saya.show', $order->id));
            }
        }

        // If a rejected request exists and cooldown is over, update it.
        if ($cancellationRequest && $cancellationRequest->status === 'rejected') {
            $cancellationRequest->update([
                'status' => 'pending',
                'reason' => $request->reason,
            ]);
        } else {
            // Otherwise, create a new request.
            CancellationRequest::create([
                'order_id' => $order->id,
                'reason' => $request->reason,
            ]);
        }

        $adminLink = route('admin.cancellations.index');

        Notification::create([
            'message' => 'Permintaan pembatalan untuk Pesanan #' . $order->id,
            'link' => $adminLink,
            'type' => 'cancellation' // Menambahkan tipe untuk pemfilteran
        ]);

        return redirect()->route('message.show')
            ->with('message_type', 'success')
            ->with('message_content', 'Permintaan pembatalan terkirim. Anda akan menerima pemberitahuan setelah ditinjau oleh admin.')
            ->with('back_url', route('pengunjung.pesanan-saya.show', $order->id));
    }

    public function checkPaymentStatus(Order $order)
    {
        if ($order->pengunjung_id !== Auth::guard('pengunjung')->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('pengunjung.pesanan-saya.show', $order)->with('info', 'Pesanan ini sudah lunas.');
        }

        // TODO: Ganti bagian ini dengan panggilan API asli ke DANA/Payment Gateway untuk memeriksa status transaksi.
        $isPaid = $this->simulateDanaApiCheck($order);

        if ($isPaid) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing' // Atau status awal setelah pembayaran berhasil
            ]);

            // Buat notifikasi untuk pelanggan
            UserNotification::create([
                'user_id' => $order->pengunjung_id,
                'order_id' => $order->id,
                'message' => 'Pembayaran untuk pesanan #' . $order->id . ' telah berhasil dan sedang diproses.',
                'link' => route('pengunjung.pesanan-saya.show', $order->id),
            ]);

            // Buat notifikasi untuk admin
            Notification::create([
                'message' => 'Pembayaran untuk Pesanan #' . $order->id . ' telah LUNAS.',
                'link' => route('admin.orders.show', $order->id),
            ]);

            return redirect()->route('pengunjung.pesanan-saya.show', $order)->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
        } else {
            return redirect()->back()->with('error', 'Pembayaran belum terdeteksi. Silakan coba lagi dalam beberapa saat.');
        }
    }

    /**
     * Mensimulasikan panggilan API untuk memeriksa status pembayaran.
     * Ganti dengan logika API call sesungguhnya.
     */
    private function simulateDanaApiCheck(Order $order)
    {
        // Ini hanya simulasi, 50/50 chance berhasil atau gagal.
        // Di aplikasi nyata, Anda akan menggunakan $order->id atau ID transaksi unik lainnya
        // untuk query status ke payment gateway.
        return rand(0, 1) === 1;
    }
}
