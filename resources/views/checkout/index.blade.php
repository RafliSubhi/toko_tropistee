@extends('layouts.app')

@section('content')
<div class="container section">
    <h2 class="text-center mb-4">Checkout</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Alamat Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form id="checkout-form" action="{{ route('pengunjung.checkout.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor Telepon</label>
                            <input type="tel" id="phone_number" name="phone_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Alamat Lengkap</label>
                            <textarea id="delivery_address" name="delivery_address" class="form-control" rows="3" required placeholder="Contoh: Jl. Jendral Sudirman No. 123, RT 01 / RW 02, Kel. Bendungan Hilir, Kec. Tanah Abang, Jakarta Pusat, 10210"></textarea>
                        </div>

                        <input type="hidden" name="shipping_cost" value="0">
                        <input type="hidden" name="delivery_lat">
                        <input type="hidden" name="delivery_lng">

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <div class="d-flex flex-wrap">
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                                    <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="dana" value="DANA">
                                    <label class="form-check-label" for="dana">Dana</label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="gopay" value="GOPAY">
                                    <label class="form-check-label" for="gopay">GoPay</label>
                                </div>
                                <div class="form-check me-3 mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="ovo" value="OVO">
                                    <label class="form-check-label" for="ovo">OVO</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($cartItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="my-0">{{ $item->product->name }}</h6>
                                    <small class="text-muted">Jumlah: {{ $item->quantity }}</small>
                                </div>
                                <span class="text-muted">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="p-3">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" form="checkout-form" class="btn btn-primary w-100">Konfirmasi Pesanan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

