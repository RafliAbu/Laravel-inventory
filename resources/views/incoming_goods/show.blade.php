@extends('layouts.master', ['title' => 'Lihat Barang Masuk'])

@section('content')
<div class="container">
    <h1>Detail Barang Masuk</h1>

    <div class="card">
        <div class="card-header">
            Informasi Transaksi
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($incomingGood->tanggal)->format('d F Y') }}</p>
                    <p><strong>Supplier:</strong> {{ $incomingGood->supplier->name }}</p>
                    <p><strong>No Surat Jalan:</strong> {{ $incomingGood->no_surat_jalan }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>RKM/PO:</strong> {{ $incomingGood->rkm_po }}</p>
                    <p><strong>PO:</strong> {{ $incomingGood->po }}</p>
                    <p><strong>Project:</strong> {{ $incomingGood->project }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Daftar Barang</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($incomingGood->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('incoming-goods.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
</div>
@endsection