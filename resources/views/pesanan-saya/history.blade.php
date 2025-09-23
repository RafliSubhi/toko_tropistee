@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4">Riwayat Pesanan Anda</h2>

            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    @forelse ($historyOrders as $order)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div>
                                <h5 class="mb-1">Pesanan #{{ $order->id }}</h5>
                                <p class="mb-1 text-muted">Tanggal Selesai: {{ $order->updated_at->format('d F Y') }}</p>
                                <p class="fw-bold">Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="{{ route('pengunjung.pesanan-saya.show', $order) }}" class="btn btn-outline-primary btn-sm me-2">Lihat Detail</a>
                                
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-muted">Anda belum memiliki riwayat pesanan.</p>
                            <a href="{{ route('menu') }}" class="btn btn-primary">Mulai Belanja</a>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-white">
                    {{ $historyOrders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
