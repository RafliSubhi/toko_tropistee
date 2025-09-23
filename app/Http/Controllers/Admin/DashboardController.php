<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Pengunjung;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $logo = DB::table('logo_dan_favicon')->first();
        $data = [
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'totalAdmins' => User::count(),
            'totalVisitors' => Pengunjung::count(),
            'logo' => $logo,
        ];

        return view('admin.dashboard', $data);
    }
}
