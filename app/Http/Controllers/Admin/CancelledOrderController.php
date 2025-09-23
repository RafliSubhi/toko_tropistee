<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Request;

class CancelledOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->input('search') . '%');
        }

        $orders = $query->where('status', 'cancelled')->latest()->paginate(15)->withQueryString();
        
        return view('admin.cancelled-orders.index', compact('orders'));
    }
}
