@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Pesanan</h1>
    <p class="mb-4">Kelola pesanan yang masuk, atur ongkos kirim, dan terima pesanan.</p>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Pesanan</h6>
        </div>
        <div class="card-body">
            <!-- Filters and Search -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="d-flex flex-wrap">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary me-2 mb-2 {{ !request()->has('filter') ? 'active' : '' }}">Semua Pending</a>
                    <a href="{{ route('admin.orders.index', ['filter' => 'belum_ongkir']) }}" class="btn btn-outline-primary me-2 mb-2 {{ request('filter') == 'belum_ongkir' ? 'active' : '' }}">Belum Ongkir</a>
                    <a href="{{ route('admin.orders.index', ['filter' => 'sudah_ongkir']) }}" class="btn btn-outline-primary me-2 mb-2 {{ request('filter') == 'sudah_ongkir' ? 'active' : '' }}">Sudah Ongkir</a>
                </div>
                <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex ms-auto mb-2">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari email..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th class="text-center">Status Pesanan</th>
                            <th class="text-center">Status Pembayaran</th>
                            <th class="text-center">Metode Pembayaran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
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
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $order->name ?? 'N/A' }}</td>
                                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="text-center"><span class="badge {{ $statusClass }}">{{ $order->indonesian_status }}</span></td>
                                <td class="text-center">
                                    @if ($order->payment_method == 'cod')
                                        <span class="badge bg-warning text-dark">Bayar di Tempat</span>
                                    @else
                                        <span class="badge {{ $paymentStatusClass }}">{{ $order->indonesian_payment_status }}</span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#showOrderModal{{ $order->id }}">
                                        <i class="bi bi-eye-fill"></i> Detail
                                    </button>
                                    @if(Auth::guard('admin')->user()->role == 'utama')
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm me-2">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus pesanan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
                                    @if($order->status == 'pending')
                                        @php
                                            $canBeAccepted = $order->shipping_cost > 0;
                                        @endphp
                                        <form action="{{ route('admin.orders.accept', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" {{ $canBeAccepted ? '' : 'disabled' }} title="{{ $canBeAccepted ? 'Terima Pesanan' : 'Ongkir harus diisi terlebih dahulu' }}">
                                                <i class="bi bi-check-circle"></i> Terima
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada pesanan yang cocok dengan filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@foreach ($orders as $order)
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
<!-- Show Order Modal -->
<div class="modal fade" id="showOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="showOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showOrderModalLabel{{ $order->id }}">Detail Pesanan #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">Silakan atur ongkos kirim jika belum diatur. Pesanan hanya bisa diterima jika ongkir sudah diisi dan status pembayaran sudah lunas (kecuali untuk metode COD).</div>
                <div class="row gy-3">
                    <div class="col-md-6">
                        <h6>Informasi Pelanggan</h6>
                        <p class="mb-1"><strong>Nama:</strong> {{ $order->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                        <p class="mb-1"><strong>No. Telepon:</strong> {{ $order->phone_number }}</p>
                        <p class="mb-0"><strong>Alamat:</strong> {{ $order->delivery_address }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pesanan</h6>
                        <p class="mb-1"><strong>Metode Pembayaran:</strong> <span class="text-uppercase">{{ $order->payment_method }}</span></p>
                        <p class="mb-1"><strong>Status Pembayaran:</strong> <span class="badge {{ $paymentStatusClass }}">{{ $order->indonesian_payment_status }}</span></p>
                        <p class="mb-0"><strong>Status Pesanan:</strong> <span class="badge {{ $statusClass }}">{{ $order->indonesian_status }}</span></p>
                    </div>
                </div>
                <hr>
                <h6>Produk yang Dipesan</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-center">{{ $product->pivot->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Subtotal Produk</td>
                                <td class="text-end fw-bold">Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Ongkos Kirim</td>
                                <td class="text-end">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="3" class="text-end fw-bold">Total Keseluruhan</td>
                                <td class="text-end fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="w-100">
                    @csrf
                    @method('PATCH')
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <label for="shipping_cost_{{ $order->id }}" class="form-label fw-bold">Atur Ongkos Kirim</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="shipping_cost" id="shipping_cost_{{ $order->id }}" class="form-control" value="{{ $order->shipping_cost ?? 0 }}" required>
                            </div>
                        </div>
                        <div class="col-sm-4 mt-3 mt-sm-0">
                             <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Simpan Ongkir</button>
                        </div>
                    </div>
                    <input type="hidden" name="status" value="{{ $order->status }}">
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection