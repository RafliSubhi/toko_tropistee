@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Edit Admin</h1>
    <p class="mb-4">Ubah data admin melalui form di bawah ini.</p>

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Form Edit Admin: {{ $user->name }}</h6>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-group">
        <label for="role">Role</label>
        <select name="role" id="role" class="form-control" {{ $user->role === 'utama' ? 'disabled' : '' }}>
            <option value="utama" {{ $user->role == 'utama' ? 'selected' : '' }}>Utama</option>
            <option value="pesanan" {{ $user->role == 'pesanan' ? 'selected' : '' }}>Pesanan</option>
            <option value="produksi" {{ $user->role == 'produksi' ? 'selected' : '' }}>Produksi</option>
            <option value="distribusi" {{ $user->role == 'distribusi' ? 'selected' : '' }}>Distribusi</option>
            <option value="finansial" {{ $user->role == 'finansial' ? 'selected' : '' }}>Finansial</option>
        </select>
        @if($user->role === 'utama')
            <small class="form-text text-muted">Role untuk admin Utama tidak dapat diubah.</small>
        @endif
    </div>
                             @if(Auth::id() === $user->id)
                                <div class="form-text text-danger">Anda tidak dapat mengubah role Anda sendiri.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
