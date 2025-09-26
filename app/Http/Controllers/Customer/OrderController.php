<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus; // Pastikan Anda meng-import Enum jika digunakan
use App\Models\Order;
use App\Models\Product;
use App\Traits\HasImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use HasImage;
    
    /**
     * Display a listing of the resource.
     * REVISI DI SINI
     */
    public function index()
    {
        // 1. Ambil data order milik user yang sedang login
        // Eager load relasi 'product' untuk performa yang lebih baik (hindari N+1 query)
        $orders = Order::with('product')
                        ->where('user_id', Auth::id())
                        ->latest() // Urutkan dari yang terbaru
                        ->paginate(10);

        // 2. Ambil SEMUA produk untuk ditampilkan di dropdown form "Tambah Permintaan"
        $products = Product::orderBy('name')->get();

        // 3. Kirim kedua variabel ('orders' dan 'products') ke view
        return view('customer.order.index', compact('orders', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     * REVISI TOTAL DI SINI
     */
    public function store(Request $request)
    {
        // Validasi input dari form baru
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Cari produk yang dipilih dari database
        $product = Product::find($request->product_id);

        // Buat data order baru berdasarkan produk yang dipilih
        Order::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id, // Simpan ID produk untuk relasi
            'name' => $product->name,     // Salin nama produk saat itu
            'unit' => $product->unit,     // Salin satuan produk saat itu
            'quantity' => $request->quantity,
            'status' => OrderStatus::Pending, // Atur status awal sebagai Pending
            // Kolom 'image' tidak perlu diisi karena gambar akan diambil dari relasi product
        ]);

        return back()->with('toast_success', 'Permintaan Barang Berhasil Diajukan');
    }

    /**
     * Update the specified resource in storage.
     * REVISI DI SINI
     */
    public function update(Request $request, Order $order)
    {
        // Pastikan user hanya bisa mengubah order miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Validasi input, hanya kuantitas yang bisa diubah
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Update hanya kuantitas
        $order->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('toast_success', 'Permintaan Barang Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Pastikan user hanya bisa menghapus order miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK');
        }

        // Hapus gambar jika ada (logika lama, mungkin tidak relevan lagi)
        if ($order->image) {
            Storage::disk('local')->delete('public/orders/'. basename($order->image));
        }

        $order->delete();

        return back()->with('toast_success', 'Permintaan Barang Berhasil Dihapus');
    }
}