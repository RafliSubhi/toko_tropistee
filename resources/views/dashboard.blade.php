@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mt-5">Admin Dashboard</h1>
    <p>Selamat datang di dashboard admin.</p>
    
    <div class="list-group mt-4">
        <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">Kelola Produk</a>
        <a href="{{ route('admin.team-members.index') }}" class="list-group-item list-group-item-action">Kelola Tim</a>
        <a href="{{ route('admin.settings.index') }}" class="list-group-item list-group-item-action">Pengaturan Toko</a>
    </div>
</div>
@endsection
