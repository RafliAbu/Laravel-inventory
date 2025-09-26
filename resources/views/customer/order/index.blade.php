@extends('layouts.master', ['title' => 'Order'])

@section('content')
    <x-container>
        <div class="col-12 col-lg-8">
            <x-card title="DAFTAR PERMINTAAN BARANG" class="card-body p-0">
                <x-table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Kuantitas</th>
                            <th>Satuan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $i => $order)
                            <tr>
                                <td>{{ $i + $orders->firstItem() }}</td>
                                <td>
                                    {{-- Mengambil gambar dari relasi product jika ada --}}
                                    <span class="avatar rounded avatar-md"
                                        style="background-image: url({{ $order->product->image ?? $order->image }})"></span>
                                </td>
                                <td>{{ $order->product->name ?? $order->name }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ $order->product->unit ?? $order->unit }}</td>
                                <td
                                    class="{{ $order->status == App\Enums\OrderStatus::Pending ? 'text-danger' : 'text-success' }}">
                                    {{ $order->status->value }}
                                </td>
                                <td>
                                    @if ($order->status == App\Enums\OrderStatus::Pending)
                                        <x-button-modal :id="$order->id" title="" icon="edit" style=""
                                            class="btn btn-info btn-sm" />
                                        <x-modal :id="$order->id" title="Ubah Data">
                                            {{-- Form edit mungkin juga perlu disesuaikan nanti --}}
                                            <form action="{{ route('customer.order.update', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <x-input name="quantity" type="number" title="Kuantitas"
                                                    placeholder="Kuantitas" :value="$order->quantity" />
                                                <x-button-save title="Simpan Perubahan" icon="save" class="btn btn-primary" />
                                            </form>
                                        </x-modal>
                                        <x-button-delete :id="$order->id" :url="route('customer.order.destroy', $order->id)" title=""
                                            class="btn btn-danger btn-sm" />
                                    @elseif($order->status == App\Enums\OrderStatus::Success && $order->product)
                                        {{-- Tombol ini hanya muncul jika permintaan diterima & terhubung ke produk --}}
                                        <form action="{{ route('cart.order', $order->product->slug) }}" method="POST">
                                            @csrf
                                            <x-button-save title="Tambahkan Keranjang" icon="shopping-cart"
                                                class="btn btn-primary btn-sm" />
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada permintaan barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        </div>
        <div class="col-lg-4 col-12">
            <x-card title="TAMBAH PERMINTAAN BARANG" class="card-body">
                {{-- PASTIKAN ANDA MENGIRIMKAN VARIABEL $products DARI CONTROLLER --}}
                <form action="{{ route('customer.order.store') }}" method="POST">
                    @csrf
                    
                    {{-- Dropdown untuk memilih produk yang sudah ada --}}
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Nama Barang</label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Barang...</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stok: {{ $product->quantity ?? 0 }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Input hanya untuk kuantitas --}}
                    <x-input name="quantity" type="number" title="Kuantitas" placeholder="Jumlah yang diminta" :value="old('quantity')" />
                    
                    <x-button-save title="Simpan Permintaan" icon="save" class="btn btn-primary" />
                </form>
            </x-card>
        </div>
    </x-container>
@endsection