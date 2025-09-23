<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product1 = Product::firstOrCreate(
            ['name' => 'Es Teh Manis Jumbo'],
            [
                'description' => 'Es teh manis dengan porsi jumbo, menyegarkan harimu.',
                'price' => 8000
            ]
        );

        $product2 = Product::firstOrCreate(
            ['name' => 'Lemon Tea Spesial'],
            [
                'description' => 'Perpaduan teh dan lemon asli yang kaya vitamin C.',
                'price' => 10000
            ]
        );

        // Get user
        $user = \App\Models\Pengunjung::first();
        if (!$user) {
            $user = \App\Models\Pengunjung::create([
                'name' => 'Pengunjung Test',
                'email' => 'pengunjung@test.com',
                'password' => Hash::make('password'),
            ]);
        }

        // 2. Buat pesanan baru
        $order = Order::create([
            'pengunjung_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'delivery_address' => 'Jl. Pahlawan No. 123, Kota Contoh, 12345',
            'payment_method' => 'QRIS',
            'shipping_cost' => 5000, // Contoh ongkir
            'status_pesanan' => 'belum diterima',
            'status_pembayaran' => 'pending',
            'delivery_lat' => -6.200000,
            'delivery_lng' => 106.816666,
            'phone_number' => '081234567890',
            'total_price' => 0, // Akan diupdate nanti
        ]);

        // 3. Tambahkan produk ke pesanan
        $totalPrice = 0;

        // Tambah produk 1
        $quantity1 = 2;
        $price1 = $product1->price;
        $totalPrice1 = $quantity1 * $price1;
        $order->products()->attach($product1->id, [
            'quantity' => $quantity1,
            'price' => $price1,
            'total_price' => $totalPrice1
        ]);
        $totalPrice += $totalPrice1;

        // Tambah produk 2
        $quantity2 = 1;
        $price2 = $product2->price;
        $totalPrice2 = $quantity2 * $price2;
        $order->products()->attach($product2->id, [
            'quantity' => $quantity2,
            'price' => $price2,
            'total_price' => $totalPrice2
        ]);
        $totalPrice += $totalPrice2;

        // 4. Update grand total pesanan
        $order->total_price = $totalPrice + $order->shipping_cost;
        $order->save();
    }
}
