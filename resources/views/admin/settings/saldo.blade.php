@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Pengaturan Saldo</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Total Saldo</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.updateSaldo') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_balance" class="form-label">Total Saldo Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="total_balance" id="total_balance" class="form-control" value="{{ old('total_balance', $totalBalance) }}" required>
                            </div>
                            <small class="text-muted">Nilai ini adalah total saldo yang ditampilkan di halaman Finansial. Mengubah nilai di sini akan menimpa nilai yang ada.</small>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
