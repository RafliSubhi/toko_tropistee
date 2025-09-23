<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - {{ $date }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            -webkit-print-color-adjust: exact; /* Chrome, Safari */
            color-adjust: exact; /* Firefox */
        }
        .print-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .order-card {
            border: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
            page-break-inside: avoid;
        }
        .order-card-header {
            background-color: #f8f9fa;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #dee2e6;
        }
        .order-card-body {
            padding: 1.25rem;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .order-card {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="print-header">
            <h1>Laporan Transaksi</h1>
            <h2>{{ $storeName }}</h2>
            <p class="lead">Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>

        <button onclick="window.print()" class="btn btn-primary mb-4 no-print">Cetak Halaman</button>

        @forelse ($orders as $order)
            <div class="order-card">
                <div class="order-card-header">
                    <h5 class="mb-0">Pesanan #{{ $order->id }} - {{ $order->name }}</h5>
                </div>
                <div class="order-card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Tanggal Selesai:</strong> {{ $order->updated_at->format('d-m-Y H:i') }}</p>
                            <p><strong>Status:</strong> {{ $order->indonesian_status }}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            <p><strong>Metode Pembayaran:</strong> {{ $order->payment_method }}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Detail Produk:</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td class="text-end">{{ $product->pivot->quantity }}</td>
                                    <td class="text-end">Rp {{ number_format($product->pivot->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($product->pivot->price * $product->pivot->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center">
                Tidak ada transaksi yang selesai pada tanggal yang dipilih.
            </div>
        @endforelse
    </div>

    <script>
        // Automatically trigger print dialog
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
