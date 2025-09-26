@extends('layouts.master', ['title' => 'Edit Barang Keluar'])

@section('content')
<div class="container">
    {{-- Judul diubah menjadi "Edit" --}}
    <h1>Edit Transaksi Barang Keluar</h1>

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
    <form action="{{ route('admin.outgoing-goods.update', $outgoingGood->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Tambahkan method PUT untuk proses update --}}

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tanggal">Tanggal</label>
                {{-- Isi value dengan data yang sudah ada --}}
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal', $outgoingGood->tanggal) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="project">Project</label>
                <input type="text" class="form-control" id="project" name="project" value="{{ old('project', $outgoingGood->project) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="no_surat_jalan">No Surat Jalan</label>
                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" value="{{ old('no_surat_jalan', $outgoingGood->no_surat_jalan) }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="do_number">DO</label>
                <input type="text" class="form-control" id="do_number" name="do_number" value="{{ old('do_number', $outgoingGood->do_number) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="jo_number">JO</label>
                <input type="text" class="form-control" id="jo_number" name="jo_number" value="{{ old('jo_number', $outgoingGood->jo_number) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="to_number">TO</label>
                <input type="text" class="form-control" id="to_number" name="to_number" value="{{ old('to_number', $outgoingGood->to_number) }}" required>
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
                @foreach ($outgoingGood->details as $index => $detail)
                    <tr>
                        <td>
                            <select class="form-control" name="products[{{ $index }}][product_id]" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $detail->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (Stok: {{ $product->quantity }})
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
        <a href="{{ route('admin.outgoing-goods.index') }}" class="btn btn-link">Batal</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addRowBtn = document.getElementById('add-product-row');
    const productTableBody = document.querySelector('#product-table tbody');
    // Mulai index dari jumlah item yang sudah ada
    let productIndex = {{ $outgoingGood->details->count() }};

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