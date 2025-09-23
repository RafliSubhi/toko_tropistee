@extends('layouts.app')

@section('content')
<style>
    .message-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
    }
    .message-card {
        max-width: 500px;
        width: 100%;
    }
    .icon-container {
        font-size: 4rem;
    }
</style>

<div class="container message-container">
    <div class="card shadow-sm message-card text-center">
        <div class="card-body p-4 p-md-5">
            @if($type === 'success')
                <div class="icon-container text-success mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h3 class="card-title">Berhasil!</h3>
            @elseif($type === 'error')
                <div class="icon-container text-danger mb-3">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <h3 class="card-title">Oops! Terjadi Kesalahan</h3>
            @else
                <div class="icon-container text-info mb-3">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <h3 class="card-title">Informasi</h3>
            @endif
            
            <p class="card-text fs-5 my-4">
                {{ $message }}
            </p>

            <a href="{{ $back_url }}" class="btn btn-primary w-100">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
