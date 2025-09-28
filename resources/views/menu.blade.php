@extends('layouts.app')

@section('content')
<div class="container section">
    <div class="row">
        <div class="col-md-12 text-center mb-5">
            <h2>Menu Kami</h2>
            <p>Pilih produk favoritmu dan tambahkan ke keranjang.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <form action="{{ route('menu') }}" method="GET">
                <div class="input-group">
                    <input type="search" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ $query ?? '' }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($product->image)
                        <img src="{{ asset('storage/app/public/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/300x250.png?text=No+Image" class="card-img-top" alt="No Image">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <p class="card-text"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></p>
                        
                        <form action="{{ route('pengunjung.cart.add', $product->id) }}" method="POST" class="mt-auto">
                            @csrf
                            <div class="mb-2">
                                <label for="quantity-{{ $product->id }}" class="form-label small">Jumlah:</label>
                                <input type="number" name="quantity" id="quantity-{{ $product->id }}" class="form-control form-control-sm" value="1" min="1">
                            </div>
                            @if(in_array($product->id, $cartItems))
                                <button type="button" class="btn btn-secondary w-100" disabled>Sudah di Keranjang</button>
                            @else
                                <button type="submit" class="btn btn-primary w-100">Masukkan Keranjang</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col">
                <p class="text-center">Produk belum tersedia.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
