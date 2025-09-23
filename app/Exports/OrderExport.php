<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date;

    public function __construct(string $date = null)
    {
        $this->date = $date;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Order::query()
                        ->where('status', 'done')
                        ->with('products'); // Eager load products

        if ($this->date) {
            $query->whereDate('updated_at', $this->date);
        }

        $orders = $query->latest('updated_at')->get();

        $exportData = collect();

        foreach ($orders as $order) {
            if ($order->products->isEmpty()) {
                // Handle case where an order might have no products
                $exportData->push([
                    'order' => $order,
                    'product' => null,
                ]);
            } else {
                foreach ($order->products as $product) {
                    $exportData->push([
                        'order' => $order,
                        'product' => $product,
                    ]);
                }
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'ID Pesanan',
            'Tanggal Selesai',
            'Nama Pelanggan',
            'Email Pelanggan',
            'Alamat Pengiriman',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Total Pesanan',
            'Ongkos Kirim',
            'ID Produk',
            'Nama Produk',
            'Jumlah',
            'Harga Satuan',
            'Subtotal Produk',
        ];
    }

    /**
    * @var array $row
    */
    public function map($row): array
    {
        $order = $row['order'];
        $product = $row['product'];

        return [
            $order->id,
            $order->updated_at->format('d-m-Y H:i:s'),
            $order->name,
            $order->email,
            $order->delivery_address,
            $order->payment_method,
            'Selesai', // Payment status is always Selesai for done orders
            $order->total_price,
            $order->shipping_cost,
            $product ? $product->id : 'N/A',
            $product ? $product->name : 'N/A',
            $product ? $product->pivot->quantity : 0,
            $product ? $product->pivot->price : 0,
            $product ? $product->pivot->quantity * $product->pivot->price : 0,
        ];
    }
}
