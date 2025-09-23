@extends('layouts.app')

@push('styles')
<style>
    .detail-row {
        background-color: #f8f9fa;
    }
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container section">
    <h2 class="text-center mb-4">Pesanan Saya</h2>

    {{-- Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @foreach($orders as $order)
        @if($order->ongkir_updated_at && !$order->ongkir_notification_seen)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Ongkos kirim untuk Pesanan <strong>#{{ $order->id }}</strong> telah diperbarui. Silakan lanjutkan ke pembayaran.
                <a href="{{ route('pengunjung.pesanan-saya.mark-ongkir-notification-seen', $order) }}" class="btn-close" aria-label="Close"></a>
            </div>
        @endif
    @endforeach

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Ongkir</th>
                            <th>Total Pembayaran</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="order-row">
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td><span class="badge bg-info text-dark">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span></td>
                                <td>{{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : '-' }}</td>
                                <td><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    {{-- Conditional Pay Button --}}
                                    @if($order->payment_method !== 'COD' && $order->status !== 'cancelled' && $order->payment_status === 'unpaid')
                                        @if($order->shipping_cost > 0)
                                            <a href="{{ route('pengunjung.pesanan-saya.pembayaran', $order) }}" class="btn btn-success btn-sm">Bayar Sekarang</a>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Admin belum menambahkan ongkos kirim">
                                                Tunggu Ongkir
                                            </button>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-dark">{{ $order->payment_method === 'COD' ? 'Cash on Delivery' : Str::title(str_replace('_', ' ', $order->payment_status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $order->id }}" aria-expanded="false" aria-controls="detail-{{ $order->id }}">
                                        Lihat Detail
                                    </button>
                                    @if($order->status == 'pending')
                                        <form action="{{ route('pengunjung.pesanan-saya.request-cancellation', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin meminta pembatalan pesanan ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Minta Batalkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            <tr class="collapse detail-row" id="detail-{{ $order->id }}">
                                <td colspan="7">
                                    <div class="p-3">
                                        <h5>Detail Pesanan #{{ $order->id }}</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Alamat Pengiriman:</strong>
                                                <p>{{ $order->delivery_address }}</p>
                                                <strong>Metode Pembayaran:</strong>
                                                <p>{{ $order->payment_method }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Rincian Biaya:</strong>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                        Subtotal
                                                        <span>Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                        Ongkos Kirim
                                                        <span>{{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : 'Belum Ditentukan' }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold">
                                                        Grand Total
                                                        <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6>Keranjang yang dipesan:</h6>
                                        <ul class="list-unstyled">
                                            @foreach($order->products as $product)
                                                <li class="d-flex align-items-center mb-3">
                                                    @php
                                                        $imageUrl = $product->image
                                                            ? asset('storage/' . $product->image)
                                                            : asset('images/default-product.png');
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="product-image me-3">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ $product->name }}</h6>
                                                        <small class="text-muted">{{ $product->pivot->quantity }} x Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</small>
                                                    </div>
                                                    <div class="fw-bold">
                                                        Rp {{ number_format($product->pivot->quantity * $product->pivot->price, 0, ',', '.') }}
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Anda belum memiliki pesanan aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('menu') }}" class="btn btn-secondary me-3">Kembali ke Menu</a>
                    {{ $orders->links() }}
                </div>
                <div class="text-end">
                    <a href="{{ route('pengunjung.pesanan-saya.history') }}" class="btn btn-outline-secondary">Lihat Riwayat Pesanan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection