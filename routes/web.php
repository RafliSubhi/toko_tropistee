<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TampilanController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CancelledOrderController;
use App\Http\Controllers\Admin\FinancialController;

use App\Http\Controllers\CartController;
use App\Http\Controllers\UserOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend Routes
Route::get('/', [TokoController::class, 'index'])->name('home');
Route::get('/menu', [TokoController::class, 'menu'])->name('menu');
Route::get('/kontak', [TokoController::class, 'kontak'])->name('kontak');
Route::get('/pesan', [TokoController::class, 'showMessage'])->name('message.show');



use App\Http\Controllers\Admin\PengunjungController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('login', [AdminLoginController::class, 'create'])->middleware('guest:admin', 'no-cache')->name('login');
    Route::post('login', [AdminLoginController::class, 'store'])->name('login.store');

    Route::middleware(['auth:admin', 'role:utama,pesanan,produksi,distribusi,finansial', 'no-cache'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::resource('products', ProductController::class);
        Route::resource('team-members', TeamMemberController::class);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
        Route::get('settings/saldo', [SettingController::class, 'saldo'])->name('settings.saldo');
        Route::post('settings/saldo', [SettingController::class, 'updateSaldo'])->name('settings.updateSaldo');
        Route::get('tampilan', [TampilanController::class, 'index'])->name('tampilan.index');
        Route::post('tampilan', [TampilanController::class, 'store'])->name('tampilan.store');
        Route::resource('pengunjung', PengunjungController::class);
        Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [AdminProfileController::class, 'update'])->name('profile.update');

        // Notifications
        Route::post('notifications/mark-all-as-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

        // Orders
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
        Route::patch('orders/{order}/accept', [\App\Http\Controllers\Admin\OrderController::class, 'accept'])->name('orders.accept');

        // Production
        Route::get('production', [\App\Http\Controllers\Admin\ProductionController::class, 'index'])->name('production.index');
        Route::patch('production/{order}/update-status', [\App\Http\Controllers\Admin\ProductionController::class, 'updateStatus'])->name('production.update-status');

        // Distribution
        Route::get('distribution', [DistributionController::class, 'index'])->name('distribution.index');
        Route::patch('distribution/{order}/update-status', [DistributionController::class, 'updateStatus'])->name('distribution.update-status');

        // Payment
        Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
        Route::patch('payment/{order}/update-status', [PaymentController::class, 'updateStatus'])->name('payment.update-status');

        // Cancelled Orders
        Route::get('cancelled-orders', [CancelledOrderController::class, 'index'])->name('cancelled-orders.index');

        // Financial
        Route::get('financial', [FinancialController::class, 'index'])->name('financial.index');
        Route::get('financial/export', [FinancialController::class, 'exportExcel'])->name('financial.export');
        Route::patch('financial/{order}/done', [FinancialController::class, 'markAsDone'])->name('financial.done');
        Route::get('financial/history', [FinancialController::class, 'history'])->name('financial.history');
        Route::get('financial/history/export', [FinancialController::class, 'exportHistory'])->name('financial.export-history');
        Route::get('financial/check-done-orders', [FinancialController::class, 'checkDoneOrders'])->name('financial.check-done-orders');
        Route::patch('financial/history/{order}', [FinancialController::class, 'updateTotalPrice'])->name('financial.updateTotalPrice');
        Route::delete('financial/history/{order}', [FinancialController::class, 'destroy'])->name('financial.destroy');

        // Cancellation
        Route::get('cancellations', [\App\Http\Controllers\Admin\CancellationController::class, 'index'])->name('cancellations.index');
        Route::patch('cancellations/{order}/approve', [\App\Http\Controllers\Admin\CancellationController::class, 'approve'])->name('cancellations.approve');
        Route::patch('cancellations/{order}/reject', [\App\Http\Controllers\Admin\CancellationController::class, 'reject'])->name('cancellations.reject');

        Route::middleware('role:utama')->group(function () {
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        });
    });
});


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// --- Visitor Auth Routes ---
Route::get('register', [RegisterController::class, 'create'])->middleware('guest:pengunjung', 'no-cache')->name('register');
Route::post('register', [RegisterController::class, 'store']);
Route::get('login', [LoginController::class, 'create'])->middleware('guest:pengunjung', 'no-cache')->name('login');
Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

use App\Http\Controllers\PengunjungProfileController;

Route::middleware(['auth:pengunjung', \App\Http\Middleware\PreventBrowserCaching::class])->name('pengunjung.')->prefix('pengunjung')->group(function () {
    Route::get('profile', [PengunjungProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [PengunjungProfileController::class, 'update'])->name('profile.update');
    Route::post('keranjang/tambah/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::get('keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::patch('keranjang/update/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('keranjang/hapus/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout
    Route::get('checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('cek-ongkir', [\App\Http\Controllers\CheckoutController::class, 'calculateOngkir'])->name('checkout.ongkir');

    // Payment (Legacy - to be integrated or removed)
    Route::get('pesanan/{order}/pembayaran', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
    Route::post('pesanan/{order}/konfirmasi-bayar', [\App\Http\Controllers\PaymentController::class, 'confirm'])->name('payment.confirm');

    // My Orders ("Pesanan Saya")
    Route::get('pesanan-saya', [UserOrderController::class, 'index'])->name('pesanan-saya.index');
    Route::get('pesanan-saya/{order}', [UserOrderController::class, 'show'])->name('pesanan-saya.show');
    Route::get('pesanan-saya/{order}/pembayaran', [UserOrderController::class, 'pembayaran'])->name('pesanan-saya.pembayaran');
    Route::post('pesanan-saya/{order}/batal', [UserOrderController::class, 'cancel'])->name('pesanan-saya.cancel');
    Route::post('pesanan-saya/{order}/request-cancellation', [UserOrderController::class, 'requestCancellation'])->name('pesanan-saya.request-cancellation');
    Route::post('pesanan-saya/{order}/konfirmasi-bayar', [UserOrderController::class, 'confirmPayment'])->name('pesanan-saya.confirm-payment');
    Route::get('pesanan-saya/{order}/cek-status', [UserOrderController::class, 'checkPaymentStatus'])->name('pesanan-saya.cek-status');

    // Order History
    Route::get('riwayat-pesanan', [UserOrderController::class, 'history'])->name('pesanan-saya.history');
    Route::post('riwayat-pesanan/{order}/hide', [UserOrderController::class, 'hideFromHistory'])->name('pesanan-saya.hide-history');
    Route::get('pesanan-saya/{order}/mark-ongkir-notification-seen', [UserOrderController::class, 'markOngkirNotificationSeen'])->name('pesanan-saya.mark-ongkir-notification-seen');
    Route::get('notifications/{notification}/mark-as-read', [\App\Http\Controllers\UserNotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-as-read', [\App\Http\Controllers\UserNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('notifications/{notification}', [\App\Http\Controllers\UserNotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [\App\Http\Controllers\UserNotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
});

