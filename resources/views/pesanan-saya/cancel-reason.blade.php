@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>Batalkan Pesanan #{{ $order->id }}</h1>

    <form action="{{ route('pengunjung.pesanan-saya.request-cancellation', $order) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="reason" class="form-label">Alasan Pembatalan</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Kirim Permintaan Pembatalan</button>
        <a href="{{ route('pengunjung.pesanan-saya.show', $order) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
