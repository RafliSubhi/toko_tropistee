@extends('layouts.app')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-header">
                    <h3>Selesaikan Pembayaran</h3>
                </div>
                <div class="card-body">
                    @if($qrCodeUrl)
                        <p>Silakan scan QR Code {{ $paymentMethod }} di bawah ini untuk membayar:</p>
                        <h4 class="my-3">Total: Rp {{ number_format($order->grand_total, 0, ',', '.') }}</h4>
                        <img src="{{ $qrCodeUrl }}" alt="QR Code {{ $paymentMethod }}" class="img-fluid rounded mb-4" style="max-width: 300px;">
                        <p class="text-muted">Setelah melakukan pembayaran, klik tombol di bawah ini.</p>
                        <form action="{{ route('payment.confirm', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">Saya Sudah Membayar</button>
                        </form>
                    @else
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Metode Pembayaran Tidak Tersedia</h4>
                            <p>Maaf, QR Code untuk {{ $paymentMethod }} belum diatur oleh admin. Silakan hubungi kami.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    ID Pesanan: {{ $order->id }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
