@extends('layouts.app')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">Pembayaran Pesanan #{{ $order->id }}</h4>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <div class="text-center mb-4">
                        <h5>Total Pembayaran:</h5>
                        <h2 class="fw-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h2>
                    </div>

                    @php
                        $paymentMethods = [
                            'qris' => isset($settings['qris_image']) && $settings['qris_image'] ? asset('storage/' . $settings['qris_image']) : null,
                            'dana' => isset($settings['dana_qr_code']) && $settings['dana_qr_code'] ? asset('storage/' . $settings['dana_qr_code']) : null,
                            'gopay' => isset($settings['gopay_qr_code']) && $settings['gopay_qr_code'] ? asset('storage/' . $settings['gopay_qr_code']) : null,
                            'ovo' => isset($settings['ovo_qr_code']) && $settings['ovo_qr_code'] ? asset('storage/' . $settings['ovo_qr_code']) : null,
                        ];
                        $availableMethods = array_filter($paymentMethods);
                    @endphp

                    @if(count($availableMethods) > 0)
                        <p class="text-center text-muted">Silakan scan salah satu QR code di bawah ini untuk menyelesaikan pembayaran.</p>

                        <!-- Payment Method Tabs -->
                        <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                            @foreach($availableMethods as $key => $value)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="pills-{{ $key }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $key }}" type="button" role="tab" aria-controls="pills-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ strtoupper($key) }}</button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="pills-tabContent">
                            @foreach($availableMethods as $key => $value)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pills-{{ $key }}" role="tabpanel" aria-labelledby="pills-{{ $key }}-tab">
                                    <div class="text-center p-3 border rounded">
                                        <img src="{{ $value }}" alt="QR Code {{ strtoupper($key) }}" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <div class="mt-3 text-center">
                            <p class="fw-bold">Setelah melakukan pembayaran, klik tombol di bawah ini:</p>
                            <form action="{{ route('pengunjung.pesanan-saya.confirm-payment', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah melakukan pembayaran? Admin akan memverifikasi bukti transfer Anda.')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">Saya Sudah Bayar</button>
                            </form>
                        </div>

                    @else
                        <div class="alert alert-warning text-center">
                            <h5 class="alert-heading">Metode Pembayaran Belum Tersedia</h5>
                            <p>Saat ini belum ada metode pembayaran yang diatur oleh admin. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <a href="{{ route('pengunjung.pesanan-saya.show', $order) }}" class="btn btn-link">Kembali ke Detail Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection