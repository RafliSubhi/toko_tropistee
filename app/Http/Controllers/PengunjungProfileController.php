<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserNotification;

class PengunjungProfileController extends Controller
{
    
    public function edit()
    {
        $user = Auth::guard('pengunjung')->user();

        $unreadNotifications = collect();
        $cartCount = 0;
        $activeOrderCount = 0;

        if ($user) {
            $unreadNotifications = UserNotification::where('user_id', $user->id)->where('is_read', false)->latest()->get();
            $cartCount = Cart::where('pengunjung_id', $user->id)->count();
            $activeOrderCount = Order::where('pengunjung_id', $user->id)->whereNotIn('status', ['completed', 'cancelled'])->count();
        }

        return view('pengunjung.profile.edit', [
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'cartCount' => $cartCount,
            'activeOrderCount' => $activeOrderCount,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('pengunjung')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pengunjungs')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('pengunjung.profile.edit')->with('status', 'profile-updated');
    }
}