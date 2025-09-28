@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Konfirmasi Pembatalan Pesanan</h1>

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
            <h6 class="m-0 fw-bold text-primary">Daftar Permintaan Pembatalan</h6>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <div class="mb-3">
                <form method="GET" action="{{ route('admin.cancellations.index') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari berdasarkan email pelanggan..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal Permintaan</th>
                            <th>Pelanggan</th>
                            <th>Alasan Pembatalan</th>
                            <th class="text-center">Status Pesanan</th>
                            <th class="text-center">Metode Pembayaran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cancellationRequests as $request)
                            @php
                                $order = $request->order;
                                $statusClass = match($order->status) {
                                    'accepted' => 'bg-success',
                                    'processing' => 'bg-primary',
                                    'pending' => 'bg-warning text-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $order->name ?? 'N/A' }}</td>
                                <td>{{ $request->reason }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }}">{{ $order->indonesian_status }}</span>
                                </td>
                                <td class="text-center"><span class="badge bg-light text-dark border">{{ strtoupper($order->payment_method) }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#showOrderModal{{ $order->id }}">
                                        <i class="bi bi-eye-fill"></i> Lihat Detail
                                    </button>
                                    <form action="{{ route('admin.cancellations.approve', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menyetujui pembatalan ini?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-circle"></i> Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.cancellations.reject', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menolak pembatalan ini?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x-circle"></i> Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada permintaan pembatalan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach ($cancellationRequests as $request)
@php 
    $order = $request->order;
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
                        <hr>
                        <h6>Alasan Pembatalan</h6>
                        <p>{{ $request->reason }}</p>
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
