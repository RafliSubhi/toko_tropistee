<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Logo;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\UserNotification;
use App\Http\View\Composers\AdminLayoutComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'auth.login', 'auth.register', 'layouts.partials.footer'], function ($view) {
            $view->with('logo', Logo::first());

            if (Auth::guard('pengunjung')->check()) {
                $user = Auth::guard('pengunjung')->user();

                // Get cart count
                $cartCount = Cart::where('pengunjung_id', $user->id)->count();

                // Get active order count
                $activeOrderCount = Order::where('pengunjung_id', $user->id)
                                         ->whereNotIn('status', ['completed', 'cancelled'])
                                         ->count();

                // Get all unread notifications for the badge count
                $unreadNotifications = UserNotification::where('user_id', $user->id)
                                                       ->whereNull('read_at')
                                                       ->get();

                // Get latest notifications for the dropdown
                $notifications = UserNotification::where('user_id', $user->id)
                                                   ->latest()
                                                   ->take(15)
                                                   ->get();

                $view->with(compact('cartCount', 'activeOrderCount', 'unreadNotifications', 'notifications'));
            } else {
                // Provide default values for guests
                $view->with([
                    'cartCount' => 0,
                    'activeOrderCount' => 0,
                    'unreadNotifications' => collect(),
                    'notifications' => collect(),
                ]);
            }
        });

        View::composer('layouts.admin', AdminLayoutComposer::class);
    }
}
