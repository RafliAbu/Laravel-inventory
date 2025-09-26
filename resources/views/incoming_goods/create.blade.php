@extends('layouts.master', ['title' => 'Tambah Barang Masuk'])


@section('content')
<div class="container">
    <h1>Tambah Transaksi Barang Masuk</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incoming-goods.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group mb-3">
                    <label for="supplier_id">Supplier</label>
                    <select class="form-control" id="supplier_id" name="supplier_id" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="form-group mb-3">
                    <label for="no_surat_jalan">No Surat Jalan</label>
                    <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" value="{{ old('no_surat_jalan') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="form-group mb-3">
                    <label for="rkm_po">RKM/PO</label>
                    <input type="text" class="form-control" id="rkm_po" name="rkm_po" value="{{ old('rkm_po') }}" required>
                </div>
                 <div class="form-group mb-3">
                    <label for="po">PO</label>
                    <input type="text" class="form-control" id="po" name="po" value="{{ old('po') }}" required>
                </div>
                <div class="form-group mb-3">
                    <label for="project">Project</label>
                    <input type="text" class="form-control" id="project" name="project" value="{{ old('project') }}" required>
                </div>
            </div>
        </div>

        <hr>

        <h3>Detail Barang</h3>
        <table class="table table-bordered" id="product-table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Baris akan ditambahkan oleh JavaScript --}}
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary" id="add-product-row">Tambah Barang</button>
        <hr>
        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        <a href="{{ route('incoming-goods.index') }}" class="btn btn-link">Batal</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addRowBtn = document.getElementById('add-product-row');
    const productTableBody = document.querySelector('#product-table tbody');
    let productIndex = 0;

    addRowBtn.addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select class="form-control" name="products[${productIndex}][product_id]" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->quantity }})</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="products[${productIndex}][quantity]" step="0.01" min="0.1" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
            </td>
        `;
        productTableBody.appendChild(newRow);
        productIndex++;
    });

    productTableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection