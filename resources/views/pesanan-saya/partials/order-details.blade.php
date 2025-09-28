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
                        ? asset('storage/app/public/' . $product->image)
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
