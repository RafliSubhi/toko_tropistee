<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DoneOrdersExport implements FromCollection, WithHeadings, WithEvents
{
    protected $data;

    public function __construct(Collection $orders)
    {
        $this->data = $this->formatData($orders);
    }

    /**
     * Transform the collection of orders into a flat collection for the Excel sheet.
     * Each row in the sheet will represent a single product item from an order.
     *
     * @param Collection $orders
     * @return Collection
     */
    private function formatData(Collection $orders): Collection
    {
        $formattedData = new Collection();

        foreach ($orders as $order) {
            if ($order->products->isEmpty()) {
                // Add a row even if there are no products, to show the order exists
                $formattedData->push([
                    'id_pesanan' => $order->id,
                    'tanggal_pesan' => $order->created_at->format('d-m-Y H:i'),
                    'nama_pelanggan' => $order->name,
                    'email_pelanggan' => $order->email,
                    'no_telepon' => $order->phone_number,
                    'alamat_pengiriman' => $order->delivery_address,
                    'metode_pembayaran' => strtoupper($order->payment_method),
                    'status_pembayaran' => $order->indonesian_payment_status,
                    'ongkos_kirim' => $order->shipping_cost,
                    'total_pesanan' => $order->total_price,
                    'nama_produk' => 'N/A',
                    'jumlah_beli' => 0,
                    'harga_satuan' => 0,
                    'subtotal_produk' => 0,
                ]);
            } else {
                // Add a row for each product in the order
                foreach ($order->products as $product) {
                    $formattedData->push([
                        'id_pesanan' => $order->id,
                        'tanggal_pesan' => $order->created_at->format('d-m-Y H:i'),
                        'nama_pelanggan' => $order->name,
                        'email_pelanggan' => $order->email,
                        'no_telepon' => $order->phone_number,
                        'alamat_pengiriman' => $order->delivery_address,
                        'metode_pembayaran' => strtoupper($order->payment_method),
                        'status_pembayaran' => $order->indonesian_payment_status,
                        'ongkos_kirim' => $order->shipping_cost,
                        'total_pesanan' => $order->total_price,
                        'nama_produk' => $product->name,
                        'jumlah_beli' => $product->pivot->quantity,
                        'harga_satuan' => $product->pivot->price,
                        'subtotal_produk' => $product->pivot->quantity * $product->pivot->price,
                    ]);
                }
            }
        }
        return $formattedData;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Pesanan',
            'Tanggal Pesan',
            'Nama Pelanggan',
            'Email Pelanggan',
            'No. Telepon',
            'Alamat Pengiriman',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Ongkos Kirim',
            'Total Pesanan',
            'Nama Produk',
            'Jumlah Beli',
            'Harga Satuan (saat beli)',
            'Subtotal Produk',
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:N1')->getFont()->setBold(true);
                foreach (range('A', 'N') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}
