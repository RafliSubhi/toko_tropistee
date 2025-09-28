@extends('layouts.app')

@section('content')
<div class="container section">
    <h2 class="text-center mb-4">Keranjang Belanja Anda</h2>

    @if($cartItems->count() > 0)
        <!-- Desktop View -->
        <div class="d-none d-md-block">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col">Harga</th>
                            <th scope="col" style="width: 15%;">Jumlah</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($cartItems as $item)
                            @php $subtotal = $item->product->price * $item->quantity; $total += $subtotal; @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100' }}" 
                                             alt="{{ $item->product->name }}" class="img-fluid rounded me-3" style="width: 100px; height: 100px; object-fit: cover;">
                                        <div>
                                            <h5 class="mb-0">{{ $item->product->name }}</h5>
                                            <small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('pengunjung.cart.update', $item->id) }}" method="POST" class="d-flex">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm me-2">
                                        <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                                    </form>
                                </td>
                                <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ route('pengunjung.cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="d-block d-md-none">
            @php $total = 0; @endphp
            @foreach($cartItems as $item)
                @php $subtotal = $item->product->price * $item->quantity; $total += $subtotal; @endphp
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100' }}" 
                                     alt="{{ $item->product->name }}" class="img-fluid rounded" style="object-fit: cover; height: 100%;">
                            </div>
                            <div class="col-8">
                                <h5 class="mb-1">{{ $item->product->name }}</h5>
                                <p class="mb-2">Harga: Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                <form action="{{ route('pengunjung.cart.update', $item->id) }}" method="POST" class="d-flex align-items-center mb-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm me-2" style="width: 70px;">
                                    <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                                </form>
                                <p class="mb-2">Subtotal: <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></p>
                                <form action="{{ route('pengunjung.cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Keranjang</h5>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-grid">
                            <a href="{{ route('pengunjung.checkout.index') }}" class="btn btn-primary">Lanjut ke Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="text-center">
            <p>Keranjang belanja Anda masih kosong.</p>
            <a href="{{ route('menu') }}" class="btn btn-primary">Mulai Belanja</a>
        </div>
    @endif
</div>
@endsection
