@extends('layouts.master', ['title' => 'Barang Masuk'])

@section('content')
<div class="container">
    <h1>Detail Barang Keluar</h1>

    <div class="card">
        <div class="card-header">
            Informasi Transaksi
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($outgoingGood->tanggal)->format('d F Y') }}</p>
                    <p><strong>Project:</strong> {{ $outgoingGood->project }}</p>
                    <p><strong>No Surat Jalan:</strong> {{ $outgoingGood->no_surat_jalan }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>DO:</strong> {{ $outgoingGood->do_number }}</p>
                    <p><strong>JO:</strong> {{ $outgoingGood->jo_number }}</p>
                    <p><strong>TO:</strong> {{ $outgoingGood->to_number }}</p>
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
            @foreach ($outgoingGood->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->quantity }} {{ $detail->product->unit }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.outgoing-goods.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar</a>
</div>
    <div class="mt-3">
        <a href="{{ route('admin.outgoing-goods.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
        
        {{-- TAMBAHKAN TOMBOL INI --}}
        <a href="{{ route('admin.outgoing-goods.print', $outgoingGood->id) }}" class="btn btn-success" target="_blank">
            <i class="fas fa-print"></i> Cetak Surat Jalan
        </a>
    </div>
</div>
@endsection