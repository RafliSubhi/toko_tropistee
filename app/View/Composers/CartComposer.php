<?php

namespace App\View\Composers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartComposer
{
    public function compose(View $view)
    {
        if (Auth::guard('pengunjung')->check()) {
            $cartCount = Cart::where('pengunjung_id', Auth::guard('pengunjung')->id())->count();
            $view->with('cartCount', $cartCount);
        } else {
            $view->with('cartCount', 0);
        }
    }
}
