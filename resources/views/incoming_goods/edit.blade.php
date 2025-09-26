@extends('layouts.master', ['title' => 'Edit Barang Masuk'])

@section('content')
<div class="container">
    {{-- Judul diubah menjadi "Edit" --}}
    <h1>Edit Transaksi Barang Masuk</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Action form diubah ke route 'update' dan method diubah ke 'PUT' --}}
    <form action="{{ route('admin.incoming-goods.update', $incomingGood->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Tambahkan method PUT untuk proses update --}}

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tanggal">Tanggal</label>
                {{-- Isi value dengan data yang sudah ada --}}
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', $incomingGood->tanggal) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="supplier_id">Supplier</label>
                <select class="form-control" id="supplier_id" name="supplier_id" required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach ($suppliers as $supplier)
                        {{-- Tambahkan kondisi 'selected' untuk data yang sudah ada --}}
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $incomingGood->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
             <div class="col-md-6 mb-3">
                <label for="no_surat_jalan">No Surat Jalan</label>
                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" value="{{ old('no_surat_jalan', $incomingGood->no_surat_jalan) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="rkm_po">RKM/PO</label>
                <input type="text" class="form-control" id="rkm_po" name="rkm_po" value="{{ old('rkm_po', $incomingGood->rkm_po) }}" required>
            </div>
             <div class="col-md-6 mb-3">
                <label for="po">PO</label>
                <input type="text" class="form-control" id="po" name="po" value="{{ old('po', $incomingGood->po) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="project">Project</label>
                <input type="text" class="form-control" id="project" name="project" value="{{ old('project', $incomingGood->project) }}" required>
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
                {{-- Tampilkan detail barang yang sudah ada --}}
                @foreach ($incomingGood->details as $index => $detail)
                    <tr>
                        <td>
                            <select class="form-control" name="products[{{ $index }}][product_id]" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $detail->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="products[{{ $index }}][quantity]" step="0.01" min="0.1" value="{{ $detail->quantity }}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="button" class="btn btn-secondary" id="add-product-row">Tambah Barang</button>
        <hr>
        <button type="submit" class="btn btn-primary">Update Transaksi</button>
        <a href="{{ route('admin.incoming-goods.index') }}" class="btn btn-link">Batal</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addRowBtn = document.getElementById('add-product-row');
    const productTableBody = document.querySelector('#product-table tbody');
    // Mulai index dari jumlah item yang sudah ada
    let productIndex = {{ $incomingGood->details->count() }};

    addRowBtn.addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select class="form-control" name="products[${productIndex}][product_id]" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
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