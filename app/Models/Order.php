<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengunjung_id',
        'tanggal',
        'name',
        'email',
        'status',
        'payment_method',
        'payment_status',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'phone_number',
        'shipping_cost',
        'total_price',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pengunjung()
    {
        return $this->belongsTo(Pengunjung::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')->withPivot('quantity', 'price');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function cancellationRequest()
    {
        return $this->hasOne(CancellationRequest::class);
    }

    /**
     * Get the Indonesian translation for the order status.
     *
     * @return string
     */
    public function getIndonesianStatusAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'accepted' => 'Pesanan Diterima',
            'processing' => 'Sedang Diproses',
            'ready_to_ship' => 'Siap Dikirim',
            'shipped' => 'Dalam Pengiriman',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'done' => 'Transaksi Selesai',
            default => 'Status Tidak Dikenal',
        };
    }

    /**
     * Get the Indonesian translation for the payment status.
     *
     * @return string
     */
    public function getIndonesianPaymentStatusAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'done' => 'Lunas',
            'unpaid' => 'Belum Dibayar',
            'waiting_confirmation' => 'Menunggu Konfirmasi Pembayaran',
            default => 'Status Tidak Dikenal',
        };
    }
}
