@extends('layouts.master', ['title' => 'Produk'])

@section('content')
    <x-container>
        <div class="row">
            <div class="col-12">
                <x-card title="TAMBAH PRODUK" class="card-body">
                    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- KODE BARANG (OTOMATIS) --}}
                        <x-input name="code" type="hidden" title="Kode Produk" placeholder="Pilih kategori untuk generate kode" :value="old('code')" :readonly="true" />

                        <x-input name="name" type="text" title="Nama Produk" placeholder="Nama Produk" :value="old('name')" />
                        <x-input name="unit" type="text" title="Satuan Produk" placeholder="Satuan Produk"
                            :value="old('unit')" />
                        <x-select title="Supplier Produk" name="supplier_id">
                            <option value>Silahkan Pilih</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </x-select>
                        
                        {{-- SELECT KATEGORI DENGAN ID DAN DATA ATTRIBUTE --}}
                        <x-select title="Kategori Produk" name="category_id" id="category-select">
                            <option value>Silahkan Pilih</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-name="{{ $category->name }}" data-count="{{ $category->products_count }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-input name="image" type="file" title="Foto Produk" placeholder="" :value="old('image')" />
                        <x-textarea name="description" title="Deskripsi Produk" placeholder="Deskripsi Produk"></x-textarea>
                        <x-button-save title="Simpan" icon="save" class="btn btn-primary" />
                        <x-button-link title="Kembali" icon="arrow-left" :url="route('admin.product.index')" class="btn btn-dark"
                            style="mr-1" />
                    </form>
                </x-card>
            </div>
        </div>
    </x-container>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category-select');
        const productCodeInput = document.querySelector('input[name="code"]');

        categorySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const categoryName = selectedOption.getAttribute('data-name');
            const productCount = parseInt(selectedOption.getAttribute('data-count'), 10);
            
            if (!categoryName) {
                productCodeInput.value = ''; // Kosongkan jika tidak ada kategori dipilih
                return;
            }

            let prefix = '';
            // Sesuaikan nama kategori di sini dengan yang ada di database Anda
            // Contoh ini menggunakan 'Consumable', 'Material', dan 'Raw Material'
            if (categoryName.toLowerCase().includes('cosumable')) { // 'cosumable' sesuai permintaan Anda
                prefix = 'CM-';
            } else if (categoryName.toLowerCase().includes('material')) {
                prefix = 'MT-';
            } else if (categoryName.toLowerCase().includes('rowmaterial')) { // 'rowmaterial' sesuai permintaan Anda
                prefix = 'RM-';
            } else {
                 // Fallback jika ada kategori lain
                prefix = 'BRG-';
            }

            // Generate nomor urut baru, padding dengan 0 hingga 3 digit
            const nextNumber = (productCount + 1).toString().padStart(3, '0');
            
            productCodeInput.value = prefix + nextNumber;
        });
    });
</script>
@endpush