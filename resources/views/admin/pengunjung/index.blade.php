@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Pengunjung</h1>
    <p class="mb-4">Daftar semua pengunjung yang telah terdaftar.</p>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Daftar Pengunjung</h6>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <div class="mb-4">
                <form action="{{ route('admin.pengunjung.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari email pengunjung..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tanggal Bergabung</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengunjungs as $pengunjung)
                            <tr>
                                <td>{{ $loop->iteration + $pengunjungs->firstItem() - 1 }}</td>
                                <td>{{ $pengunjung->name }}</td>
                                <td>{{ $pengunjung->email }}</td>
                                <td>{{ $pengunjung->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.pengunjung.edit', $pengunjung->id) }}" class="btn btn-warning btn-sm me-2"><i class="bi bi-pencil-square"></i> Edit</a>
                                    <form action="{{ route('admin.pengunjung.destroy', $pengunjung->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengunjung ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada pengunjung yang ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($pengunjungs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $pengunjungs->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
