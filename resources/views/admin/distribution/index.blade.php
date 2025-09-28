@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Distribusi Pesanan</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Pesanan Siap Kirim & Dalam Pengiriman</h6>
        </div>
        <div class="card-body">
            <!-- Filters and Search -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="d-flex flex-wrap">
                    <a href="{{ route('admin.distribution.index') }}" class="btn btn-outline-primary me-2 mb-2 {{ !request('status') ? 'active' : '' }}">Semua</a>
                    <a href="{{ route('admin.distribution.index', ['status' => 'ready_to_ship']) }}" class="btn btn-outline-primary me-2 mb-2 {{ request('status') == 'ready_to_ship' ? 'active' : '' }}">Siap Dikirim</a>
                    <a href="{{ route('admin.distribution.index', ['status' => 'shipped']) }}" class="btn btn-outline-primary me-2 mb-2 {{ request('status') == 'shipped' ? 'active' : '' }}">Dalam Pengiriman</a>
                </div>
                <form method="GET" action="{{ route('admin.distribution.index') }}" class="d-flex ms-auto mb-2">
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
                                    'ready_to_ship' => 'bg-primary',
                                    'shipped' => 'bg-info text-dark',
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
                                <td class="text-center"><span class="badge {{ $statusClass }}">{{ $order->indonesian_status }}</span></td>
                                <td class="text-center">
                                    <span class="badge {{ $paymentStatusClass }}">{{ $order->indonesian_payment_status }}</span>
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
                                    @if($order->status == 'ready_to_ship')
                                        <form action="{{ route('admin.distribution.update-status', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Ubah status menjadi Dalam Pengiriman?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="shipped">
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-truck"></i> Kirim</button>
                                        </form>
                                    @elseif($order->status == 'shipped')
                                        <form action="{{ route('admin.distribution.update-status', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi pesanan telah sampai di tujuan?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-all"></i> Selesai</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada pesanan yang perlu ditangani.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach ($orders as $order)
@php 
    $statusClass = match($order->status) {
        'completed', 'done' => 'bg-success',
        'shipped' => 'bg-info text-dark',
        'processing' => 'bg-primary',
        'ready_to_ship' => 'bg-primary',
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection