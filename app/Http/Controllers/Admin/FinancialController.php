<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\Exports\OrderExport;
use App\Exports\DoneOrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date');
        $search = $request->input('search');

        $ordersQuery = Order::query()->where('status', 'completed');

        if ($date) {
            $ordersQuery->whereDate('updated_at', $date);
        }

        if ($search) {
            $ordersQuery->where('email', 'like', '%' . $search . '%');
        }

        $completedOrders = $ordersQuery->latest()->paginate(15)->withQueryString();

        $totalBalanceSetting = Setting::firstOrCreate(['key' => 'total_balance'], ['value' => '0']);
        $totalBalance = $totalBalanceSetting->value;

        return view('admin.financial.index', compact('completedOrders', 'totalBalance', 'date', 'search'));
    }

    public function markAsDone(Order $order)
    {
        if ($order->status !== 'completed') {
            return back()->with('error', 'Hanya pesanan yang sudah selesai yang bisa ditandai.');
        }

        $order->update(['status' => 'done', 'payment_status' => 'paid']);

        Setting::where('key', 'total_balance')->lockForUpdate()->increment('value', $order->total_price);

        UserNotification::create([
            'user_id' => $order->pengunjung_id,
            'order_id' => $order->id,
            'message' => "Transaksi untuk pesanan #{$order->id} telah selesai sepenuhnya.",
            'link' => route('pengunjung.pesanan-saya.history'),
        ]);

        return redirect()->route('admin.financial.index')->with('success', 'Pesanan #' . $order->id . ' telah ditandai sebagai Done.');
    }

    public function exportExcel(Request $request)
    {
        $date = $request->input('date');
        $fileName = 'laporan-riwayat-transaksi';

        if ($date) {
            $fileName .= '-' . $date;
        }
        $fileName .= '.xlsx';

        return Excel::download(new OrderExport($date), $fileName);
    }

    public function history(Request $request)
    {
        $date = $request->input('date');
        $search = $request->input('search');

        $ordersQuery = Order::query()->where('status', 'done');

        if ($date) {
            $ordersQuery->whereDate('updated_at', $date);
        }

        if ($search) {
            $ordersQuery->where('email', 'like', '%' . $search . '%');
        }

        $doneOrders = $ordersQuery->latest()->paginate(15)->withQueryString();

        return view('admin.financial.history', compact('doneOrders', 'date', 'search'));
    }

    public function updateTotalPrice(Request $request, Order $order)
    {
        if ($order->status !== 'done') {
            return back()->with('error', "Hanya total harga pada transaksi yang sudah 'Done' yang bisa diubah.");
        }

        $validated = $request->validate([
            'total_price' => 'required|numeric|min:0',
        ]);

        $order->update([
            'total_price' => $validated['total_price'],
        ]);

        return redirect()->route('admin.financial.history')->with('success', 'Total harga untuk pesanan #' . $order->id . ' berhasil diperbarui.');
    }

    public function destroy(Order $order)
    {
        if ($order->status !== 'done') {
            return back()->with('error', "Hanya transaksi yang sudah 'Done' yang bisa dihapus.");
        }
        
        $order->delete();

        return redirect()->route('admin.financial.history')->with('success', 'Transaksi pesanan #' . $order->id . ' berhasil dihapus.');
    }

    public function exportHistory(Request $request)
    {
        $request->validate([
            'export_date' => 'required|date',
        ]);

        $date = $request->export_date;

        $orders = Order::where('status', 'done')
                        ->whereDate('created_at', $date)
                        ->latest()
                        ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pesanan di tanggal ' . $date);
        }

        return Excel::download(new DoneOrdersExport($orders), 'financial-history-' . $date . '.xlsx');
    }

    public function checkDoneOrders(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $exists = Order::where('status', 'done')
                         ->whereDate('created_at', $request->date)
                         ->exists();

        return response()->json(['exists' => $exists]);
    }
}
