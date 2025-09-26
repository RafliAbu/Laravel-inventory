<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Traits\HasImage;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    use HasImage;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'product'])->latest()->paginate(10);
        $categories = Category::get();
        $suppliers = Supplier::get();

        return view('admin.order.index', compact('orders', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // =================================================================
        // HANYA PROSES ORDER YANG MASIH BERSTATUS PENDING
        // =================================================================
        if ($order->status == OrderStatus::Pending) {
            
            // REVISI: Tambahkan logika untuk aksi 'TOLAK'
            if ($request->input('action') === 'reject') {
                $order->status = OrderStatus::Rejected;
                $order->save();
                return back()->with('toast_success', 'Permintaan telah ditolak.');
            }

            // Logika untuk aksi 'KONFIRMASI' (default)
            // (Kode Anda di sini sudah benar)
            $product = $order->product;

            if (!$product) {
                return back()->with('toast_error', 'Order ini tidak terhubung ke produk manapun.');
            }
            if ($product->quantity < $order->quantity) {
                return back()->with('toast_error', 'Stok tidak mencukupi. Sisa stok: ' . $product->quantity);
            }

            $product->quantity -= $order->quantity;
            $product->save();

            $order->status = OrderStatus::Verified;
            $order->save();

            return back()->with('toast_success', 'Order berhasil dikonfirmasi dan stok telah diperbarui.');
        }
        
        // =================================================================
        // AKSI UNTUK MEMBUAT PRODUK DARI ORDER YANG SUDAH TERVERIFIKASI
        // (Kode Anda di sini sudah benar)
        // =================================================================
        elseif ($order->status == OrderStatus::Verified) {
            
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'quantity' => 'required|integer|min:0',
                'unit' => 'required|string|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'description' => 'nullable|string',
            ]);

            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $this->uploadImage($request, $path = 'public/products/', $name = 'image');
                $imageUrl = $image->hashName();
            }

            Product::create([
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
                'name' => $request->name,
                'image' => $imageUrl,
                'unit' => $request->unit,
                'description' => $request->description,
                'quantity' => $request->quantity
            ]);

            $order->status = OrderStatus::Success;
            $order->save();

            return back()->with('toast_success', 'Produk baru berhasil ditambahkan dari permintaan.');
        }

        return back();
    }
    
    // ... method lainnya ...
}