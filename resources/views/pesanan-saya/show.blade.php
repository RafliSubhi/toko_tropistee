@extends('layouts.app')

@section('content')
<div class="container section">
    <div class="mb-4">
        <a href="{{ route('pengunjung.pesanan-saya.index') }}" class="btn btn-secondary">&laquo; Kembali ke Daftar Pesanan</a>
    </div>
    <h2 class="text-center mb-4">Detail Pesanan #{{ $order->id }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5>Item yang Dipesan</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($order->products as $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="my-0">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->pivot->quantity }} x Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</small>
                            </div>
                            <strong>Rp {{ number_format($product->pivot->quantity * $product->pivot->price, 0, ',', '.') }}</strong>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($order->payment_status == 'unpaid' && $payment_qr_code_url)
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5>Pembayaran {{ strtoupper($order->payment_method) }}</h5></div>
                <div class="card-body text-center">
                    <p>Silakan scan kode QR di bawah ini untuk menyelesaikan pembayaran.</p>
                    <img src="{{ $payment_qr_code_url }}" alt="{{ $order->payment_method }} Code" class="img-fluid" style="max-width: 300px;">
                    <hr>
                    <p class="mt-3">Setelah melakukan pembayaran, klik tombol di bawah ini.</p>
                    <form action="{{ route('pengunjung.pesanan-saya.confirm-payment', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Saya Sudah Bayar</button>
                    </form>
                </div>
            </div>
            @endif

        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-header"><h5>Ringkasan</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span>ID Pesanan:</span> <strong>#{{ $order->id }}</strong></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Tanggal:</span> <strong>{{ $order->created_at->format('d M Y') }}</strong></li>
                        @php
                            $statusClass = match($order->status) {
                                'completed', 'done' => 'bg-success',
                                'shipped', 'processing', 'accepted' => 'bg-primary',
                                'pending' => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                            $paymentStatusClass = match($order->payment_status) {
                                'paid' => 'bg-success',
                                'unpaid' => 'bg-danger',
                                'waiting_confirmation' => 'bg-warning text-dark',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <li class="list-group-item d-flex justify-content-between"><span>Status Pesanan:</span> <span class="badge {{ $statusClass }}">{{ $order->indonesian_status }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Status Pembayaran:</span> <span class="badge {{ $paymentStatusClass }}">{{ $order->indonesian_payment_status }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Metode Pembayaran:</span> <strong>{{ $order->payment_method }}</strong></li>
                    </ul>
                    <hr>
                     <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><span>Subtotal:</span> <span>Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><span>Ongkir:</span> <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></li>
                        <li class="list-group-item d-flex justify-content-between fs-5"><span>Total:</span> <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></li>
                    </ul>
                    <hr>
                    <p><strong>Alamat Pengiriman:</strong><br>{{ $order->delivery_address }}</p>
                    <p><strong>No. Telepon:</strong><br>{{ $order->phone_number }}</p>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
