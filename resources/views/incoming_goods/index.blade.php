@extends('layouts.master', ['title' => 'Barang Masuk'])

@section('content')
<div class="container">
    <h1>Daftar Barang Masuk</h1>
     <div class="mb-3">
        <a href="{{ route('admin.incoming-goods.create') }}" class="btn btn-primary">Tambah Transaksi</a>
        
        {{-- TOMBOL BARU UNTUK CETAK PDF --}}
        <a href="{{ route('admin.incoming-goods.report.pdf', request()->query()) }}" class="btn btn-success" target="_blank">
            Cetak PDF
        </a>
    </div>

    {{-- Panel Filter Pencarian --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Filter Pencarian</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('incoming-goods.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_tanggal">Tanggal</label>
                            <input type="date" name="search_tanggal" id="search_tanggal" class="form-control" value="{{ request('search_tanggal') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_supplier">Supplier</label>
                            <select name="search_supplier" id="search_supplier" class="form-control">
                                <option value="">-- Semua Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('search_supplier') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_barang">Barang</label>
                            <select name="search_barang" id="search_barang" class="form-control">
                                <option value="">-- Semua Barang --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ request('search_barang') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_surat_jalan">Nomor Surat Jalan</label>
                            <input type="text" name="search_surat_jalan" id="search_surat_jalan" class="form-control" placeholder="No. Surat Jalan..." value="{{ request('search_surat_jalan') }}">
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                    <a href="{{ route('incoming-goods.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
                            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Surat Jalan</th>
                <th>Supplier</th>
                <th>Project</th>
{{-- KOLOM BARU DITAMBAHKAN --}}
                <th>RKM/PO</th>
                <th>PO</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incomingGoods as $item)
                <tr>
                    <td>{{ $loop->iteration + $incomingGoods->firstItem() - 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->no_surat_jalan }}</td>
                    <td>{{ $item->supplier->name }}</td>
                    <td>{{ $item->project }}</td>
                    {{-- DATA BARU DITAMPILKAN --}}
                    <td>{{ $item->rkm_po }}</td>
                    <td>{{ $item->po }}</td>
                    <td>
    <a href="{{ route('admin.incoming-goods.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>

    {{-- PANGGIL DENGAN NAMA YANG BENAR --}}
    @can('update-incoming-good')
<x-button-link title="" icon="edit" class="btn btn-warning btn-sm"
    :url="route('admin.incoming-goods.edit', $item->id)" style="" />
    @endcan

    {{-- PANGGIL DENGAN NAMA YANG BENAR --}}
    @can('delete-incoming-good')
        <x-button-delete :id="$item->id" :url="route('admin.incoming-goods.destroy', $item->id)"
            title="" class="btn btn-danger btn-sm" />
    @endcan
</td>
                </tr>
            @empty
                <tr>
                    {{-- Sesuaikan colspan menjadi 8 --}}
                    <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $incomingGoods->links() }}
</div>
@endsection