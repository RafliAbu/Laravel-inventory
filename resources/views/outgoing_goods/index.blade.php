@extends('layouts.master', ['title' => 'Barang Keluar'])

@section('content')
<div class="container">
    <h1>Daftar Barang Keluar</h1>
        <div class="mb-3">
        <a href="{{ route('admin.outgoing-goods.create') }}" class="btn btn-primary">Tambah Transaksi</a>
        
        {{-- TOMBOL BARU UNTUK CETAK PDF --}}
        <a href="{{ route('admin.outgoing-goods.report.pdf', request()->query()) }}" class="btn btn-success" target="_blank">
            Cetak PDF
        </a>
    </div>

    {{-- Panel Filter Pencarian --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Filter Pencarian</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.outgoing-goods.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_tanggal">Tanggal</label>
                            <input type="date" name="search_tanggal" id="search_tanggal" class="form-control" value="{{ request('search_tanggal') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search_project">Project</label>
                            <input type="text" name="search_project" id="search_project" class="form-control" placeholder="Nama Project..." value="{{ request('search_project') }}">
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
                    <a href="{{ route('admin.outgoing-goods.index') }}" class="btn btn-light">Reset</a>
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
                <th>Project</th>
                {{-- KOLOM BARU DITAMBAHKAN --}}
                <th>DO</th>
                <th>JO</th>
                <th>TO</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($outgoingGoods as $item)
                <tr>
                    <td>{{ $loop->iteration + $outgoingGoods->firstItem() - 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->no_surat_jalan }}</td>
                    <td>{{ $item->project }}</td>
                    {{-- DATA BARU DITAMPILKAN --}}
                    <td>{{ $item->do_number }}</td>
                    <td>{{ $item->jo_number }}</td>
                    <td>{{ $item->to_number }}</td>
                    <td>
    <a href="{{ route('admin.outgoing-goods.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>

    {{-- Tombol Edit --}}
    @can('update-outgoing-good') {{-- Ganti dengan nama permission yang sesuai --}}
        <x-button-link title="" icon="edit" class="btn btn-warning btn-sm"
            :url="route('admin.outgoing-goods.edit', $item->id)" style="" />
    @endcan

    {{-- Tombol Hapus --}}
    @can('delete-outgoing-good') {{-- Ganti dengan nama permission yang sesuai --}}
        <x-button-delete :id="$item->id" :url="route('admin.outgoing-goods.destroy', $item->id)"
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

    {{-- Link paginasi ini akan otomatis membawa parameter filter --}}
    {{ $outgoingGoods->links() }}
</div>
@endsection