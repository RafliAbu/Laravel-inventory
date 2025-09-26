@extends('layouts.master', ['title' => 'Barang'])

@section('content')
    <x-container>
        <div class="col-12">
            @can('create-product')
                <x-button-link title="Tambah Barang" icon="plus" class="btn btn-primary mb-3" style="mr-1" :url="route('admin.product.create')" />
            @endcan
            
            <x-card title="DAFTAR BARANG">

                {{-- Form untuk Filter dan Jumlah Data --}}
                <form action="{{ route('admin.product.index') }}" method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">
                        {{-- Dropdown Jumlah Data --}}
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Tampilkan</label>
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200</option>
                                <option value="-1" {{ request('per_page') == -1 ? 'selected' : '' }}>Semua</option>
                            </select>
                        </div>
                        
                        {{-- Filter Nama Barang --}}
                        <div class="col-md-4">
                            <label for="search_product" class="form-label">Nama Barang</label>
                            <input type="text" name="search_product" id="search_product" class="form-control" value="{{ request('search_product') }}" placeholder="Cari nama barang...">
                        </div>
                        
                        {{-- Filter Nama Supplier --}}
<div class="col-md-3">
                            <label for="supplier_id" class="form-label">Nama Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-select">
                                <option value="">Semua Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="col-md-2 d-flex">
                            <button type="submit" class="btn btn-primary me-2 w-100">Filter</button>
                            <a href="{{ route('admin.product.index') }}" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="card-body p-0">
                    <x-table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Barang</th>
                                <th>Foto</th>
                                <th>Nama Barang</th>
                                <th>Nama Supplier</th>
                                <th>Kategori Barang</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $i => $product)
                                <tr>
                                    <td>{{ $i + $products->firstItem() }}</td>
                                    <td>{{ $product->code ?? 'N/A' }}</td> {{-- Menampilkan kode barang --}}
                                    <td>
                                        <span class="avatar rounded avatar-md"
                                            style="background-image: url({{ $product->image }})"></span>
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->supplier->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td>
                                  @can('update-product')
                                        <x-button-link title="" icon="edit" class="btn btn-info btn-sm"
                                            :url="route('admin.product.edit', $product->id)" style="" />
                                    @endcan
                                    @can('delete-product')
                                        <x-button-delete :id="$product->id" :url="route('admin.product.destroy', $product->id)" title=""
                                            class="btn btn-danger btn-sm" />
                                    @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>
                
                {{-- Link Paginasi --}}
                <div class="mt-3">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </x-card>
        </div>
    </x-container>
@endsection