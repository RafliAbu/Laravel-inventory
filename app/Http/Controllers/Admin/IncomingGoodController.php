<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomingGood;
use App\Models\IncomingGoodDetail;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class IncomingGoodController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang masuk.
     */
    public function index()
    {
        // Ambil data untuk filter dropdown
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $query = IncomingGood::with('supplier');

        // ... (kode filter Anda sudah benar) ...
        $query->when(request('search_tanggal'), fn($q, $t) => $q->where('tanggal', $t));
        $query->when(request('search_supplier'), fn($q, $s) => $q->where('supplier_id', $s));
        $query->when(request('search_surat_jalan'), fn($q, $n) => $q->where('no_surat_jalan', 'like', "%{$n}%"));
        $query->when(request('search_barang'), function ($q, $product_id) {
            return $q->whereHas('details', fn($sub) => $sub->where('product_id', $product_id));
        });

        $incomingGoods = $query->latest()->paginate(10)->withQueryString();

        return view('incoming_goods.index', compact('incomingGoods', 'suppliers', 'products'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('incoming_goods.create', compact('suppliers', 'products'));
    }

    /**
     * Menyimpan transaksi barang masuk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'no_surat_jalan' => 'required|string|max:255',
            'rkm_po' => 'required|string|max:255',
            'po' => 'required|string|max:255',
            'project' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            $incomingGood = IncomingGood::create($request->only(
                'tanggal', 'supplier_id', 'no_surat_jalan', 'rkm_po', 'po', 'project'
            ));

            foreach ($request->products as $productData) {
                IncomingGoodDetail::create([
                    'incoming_good_id' => $incomingGood->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);

                $product = Product::find($productData['product_id']);
                $product->increment('quantity', $productData['quantity']); // Lebih aman menggunakan increment
            }
            
            DB::commit();
            return redirect()->route('incoming-goods.index')->with('success', 'Transaksi barang masuk berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail transaksi barang masuk.
     */
    public function show(IncomingGood $incomingGood)
    {
        $incomingGood->load('details.product', 'supplier');
        return view('incoming_goods.show', compact('incomingGood'));
    }

    /**
     * Menampilkan form untuk mengedit transaksi barang masuk.
     */
    public function edit(IncomingGood $incomingGood)
    {
        $incomingGood->load('details'); // Load detail barang yang sudah ada
        $suppliers = Supplier::all();
        $products = Product::all();

        return view('incoming_goods.edit', compact('incomingGood', 'suppliers', 'products'));
    }

    /**
     * Memperbarui transaksi barang masuk di database.
     */
    public function update(Request $request, IncomingGood $incomingGood)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'no_surat_jalan' => 'required|string|max:255',
            'rkm_po' => 'required|string|max:255',
            'po' => 'required|string|max:255',
            'project' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Kembalikan stok lama ke kondisi semula
            foreach ($incomingGood->details as $detail) {
                $product = Product::find($detail->product_id);
                $product->decrement('quantity', $detail->quantity); // Kurangi stok lama
            }

            // 2. Hapus detail transaksi yang lama
            $incomingGood->details()->delete();

            // 3. Update data utama transaksi
            $incomingGood->update($request->only(
                'tanggal', 'supplier_id', 'no_surat_jalan', 'rkm_po', 'po', 'project'
            ));

            // 4. Tambahkan detail baru dan update stok baru
            foreach ($request->products as $productData) {
                IncomingGoodDetail::create([
                    'incoming_good_id' => $incomingGood->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);

                $product = Product::find($productData['product_id']);
                $product->increment('quantity', $productData['quantity']); // Tambah stok baru
            }

            DB::commit();
            return redirect()->route('incoming-goods.index')->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus transaksi barang masuk dari database.
     */
    public function destroy(IncomingGood $incomingGood)
    {
        DB::beginTransaction();
        try {
            // 1. Kembalikan (kurangi) stok barang sebelum menghapus transaksi
            foreach ($incomingGood->details as $detail) {
                $product = Product::find($detail->product_id);
                // Pastikan stok tidak menjadi minus (meski seharusnya tidak terjadi)
                if ($product && $product->quantity >= $detail->quantity) {
                    $product->decrement('quantity', $detail->quantity);
                }
            }

            // 2. Hapus transaksi (detail akan terhapus otomatis karena cascade)
            $incomingGood->delete();
            
            DB::commit();
            return redirect()->route('incoming-goods.index')->with('success', 'Transaksi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
     public function generatePDF(Request $request)
    {
        // 1. Logika query yang sama persis dengan method index() untuk mengambil data yang terfilter
        $query = IncomingGood::with('supplier');

        $query->when($request->search_tanggal, fn($q, $t) => $q->where('tanggal', $t));
        $query->when($request->search_supplier, fn($q, $s) => $q->where('supplier_id', $s));
        $query->when($request->search_surat_jalan, fn($q, $n) => $q->where('no_surat_jalan', 'like', "%{$n}%"));
        $query->when($request->search_barang, function ($q, $product_id) {
            return $q->whereHas('details', fn($sub) => $sub->where('product_id', $product_id));
        });

        // Ambil SEMUA data yang cocok (tanpa paginasi)
        $incomingGoods = $query->latest()->get();

        // Data tambahan untuk ditampilkan di laporan
        $data = [
            'title' => 'Laporan Daftar Barang Masuk',
            'date' => date('d/m/Y'),
            'incomingGoods' => $incomingGoods
        ];

        // 2. Load view dan data, lalu generate PDF
        $pdf = PDF::loadView('incoming_goods.report_pdf', $data)->setPaper('a4', 'landscape');
        
        // 3. Download file PDF dengan nama spesifik
        $fileName = 'Laporan-Barang-Masuk-' . Carbon::now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }
}