@extends('layouts.master', ['title' => 'Order'])

@section('content')
    <x-container>
        <div class="col-12">
            <x-card title="DAFTAR PERMINTAAN BARANG" class="card-body p-0">
                <x-table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pemesan</th>
                            <th>Nama Barang</th>
                            <th>Kuantitas</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $i => $order)
                            <tr>
                                <td>{{ $i + $orders->firstItem() }}</td>
                                <td>{{ $order->user->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar rounded avatar-md me-3"
                                            style="background-image: url({{ $order->image }})"></span>
                                        <div>
                                            {{-- Prioritaskan nama dari produk, fallback ke nama di order --}}
                                            <div>{{ $order->product->name ?? $order->name }}</div>
                                            <div class="text-muted">{{ $order->product->code ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $order->quantity }} {{ $order->product->unit ?? $order->unit }}</td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    {{-- Logika badge status yang lebih baik --}}
                                    @php
                                        $statusClass = match($order->status) {
                                            App\Enums\OrderStatus::Pending => 'bg-warning-lt',
                                            App\Enums\OrderStatus::Verified => 'bg-info-lt',
                                            App\Enums\OrderStatus::Success => 'bg-success-lt',
                                            App\Enums\OrderStatus::Rejected => 'bg-danger-lt',
                                            default => 'bg-secondary-lt',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $order->status->value }}</span>
                                </td>
                                <td>
                                    {{-- Aksi disederhanakan sesuai status --}}
                                    @if ($order->status == App\Enums\OrderStatus::Pending)
                                        <div class="d-flex">
                                            {{-- Form untuk Konfirmasi --}}
                                            <form action="{{ route('admin.order.update', $order->id) }}" method="POST"
                                                onsubmit="return confirm('Konfirmasi permintaan ini? Stok akan dikurangi.');" class="me-2">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="verify">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-check me-1"></i> Konfirmasi
                                                </button>
                                            </form>
                                            
                                            {{-- Form untuk Tolak --}}
                                            <form action="{{ route('admin.order.update', $order->id) }}" method="POST"
                                                onsubmit="return confirm('Anda yakin ingin menolak permintaan ini?');">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times me-1"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif ($order->status == App\Enums\OrderStatus::Verified)
                                        <span class="text-muted fst-italic">
                                            <i class="fas fa-check-circle text-info"></i> Stok sudah dialokasikan
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">Tidak ada aksi</span>
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
    </x-container>
@endsection