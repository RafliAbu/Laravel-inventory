<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutgoingGood;
use App\Models\OutgoingGoodDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class OutgoingGoodController extends Controller
{
    /**
     * Menampilkan daftar transaksi barang keluar.
     */
    public function index()
    {
        $products = Product::orderBy('name')->get();
        $query = OutgoingGood::query();

        $query->when(request('search_tanggal'), fn($q, $t) => $q->where('tanggal', $t));
        $query->when(request('search_project'), fn($q, $p) => $q->where('project', 'like', "%{$p}%"));
        $query->when(request('search_surat_jalan'), fn($q, $n) => $q->where('no_surat_jalan', 'like', "%{$n}%"));
        $query->when(request('search_barang'), function ($q, $product_id) {
            return $q->whereHas('details', fn($sub) => $sub->where('product_id', $product_id));
        });

        $outgoingGoods = $query->latest()->paginate(10)->withQueryString();

        return view('outgoing_goods.index', compact('outgoingGoods', 'products'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $now = Carbon::now();
        $year = $now->format('Y');
        $month = $now->format('m');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romanMonths[$now->month - 1];
        $nextOrderNumber = OutgoingGood::count() + 1;
        $paddedOrderNumber = str_pad($nextOrderNumber, 4, '0', STR_PAD_LEFT);
        $nomorSuratJalan = "SJ-{$paddedOrderNumber}/{$month}/{$romanMonth}/{$year}";

        $products = Product::where('quantity', '>', 0)->get();

        return view('outgoing_goods.create', compact('products', 'nomorSuratJalan'));
    }

    /**
     * Menyimpan transaksi barang keluar baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'project' => 'required|string|max:255',
            'no_surat_jalan' => 'required|string|max:255|unique:outgoing_goods,no_surat_jalan',
            'do_number' => 'required|string|max:255',
            'jo_number' => 'required|string|max:255',
            'to_number' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            $outgoingGood = OutgoingGood::create($request->only(
                'tanggal', 'project', 'no_surat_jalan', 'do_number', 'jo_number', 'to_number'
            ));

            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                if ($product->quantity < $productData['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->quantity}");
                }
                $product->decrement('quantity', $productData['quantity']);

                OutgoingGoodDetail::create([
                    'outgoing_good_id' => $outgoingGood->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.outgoing-goods.index')->with('success', 'Transaksi barang keluar berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail transaksi barang keluar.
     */
    public function show(OutgoingGood $outgoingGood)
    {
        $outgoingGood->load('details.product');
        return view('outgoing_goods.show', compact('outgoingGood'));
    }

    /**
     * Menampilkan form untuk mengedit transaksi barang keluar.
     */
    public function edit(OutgoingGood $outgoingGood)
    {
        $outgoingGood->load('details');
        $products = Product::all(); // Tampilkan semua produk saat edit
        return view('outgoing_goods.edit', compact('outgoingGood', 'products'));
    }

    /**
     * Memperbarui transaksi barang keluar di database.
     */
    public function update(Request $request, OutgoingGood $outgoingGood)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'project' => 'required|string|max:255',
            'no_surat_jalan' => 'required|string|max:255|unique:outgoing_goods,no_surat_jalan,' . $outgoingGood->id,
            'do_number' => 'required|string|max:255',
            'jo_number' => 'required|string|max:255',
            'to_number' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Kembalikan stok lama ke kondisi semula (tambahkan kembali)
            foreach ($outgoingGood->details as $detail) {
                $product = Product::find($detail->product_id);
                $product->increment('quantity', $detail->quantity);
            }

            // 2. Hapus detail transaksi yang lama
            $outgoingGood->details()->delete();

            // 3. Update data utama transaksi
            $outgoingGood->update($request->only(
                'tanggal', 'project', 'no_surat_jalan', 'do_number', 'jo_number', 'to_number'
            ));

            // 4. Tambahkan detail baru dan kurangi stok baru
            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                if ($product->quantity < $productData['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->quantity}");
                }
                $product->decrement('quantity', $productData['quantity']);

                OutgoingGoodDetail::create([
                    'outgoing_good_id' => $outgoingGood->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.outgoing-goods.index')->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus transaksi barang keluar dari database.
     */
    public function destroy(OutgoingGood $outgoingGood)
    {
        DB::beginTransaction();
        try {
            // 1. Kembalikan (tambahkan) stok barang sebelum menghapus transaksi
            foreach ($outgoingGood->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->increment('quantity', $detail->quantity);
                }
            }

            // 2. Hapus transaksi (detail akan terhapus otomatis karena cascade)
            $outgoingGood->delete();
            
            DB::commit();
            return redirect()->route('admin.outgoing-goods.index')->with('success', 'Transaksi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman cetak Surat Jalan.
     */
    public function print(OutgoingGood $outgoingGood)
    {
        $outgoingGood->load('details.product');
        return view('outgoing_goods.print', compact('outgoingGood'));
    }
     public function generatePDF(Request $request)
    {
        // 1. Logika query yang sama persis dengan method index() untuk mengambil data yang terfilter
        $query = OutgoingGood::query();

        $query->when($request->search_tanggal, fn($q, $t) => $q->where('tanggal', $t));
        $query->when($request->search_project, fn($q, $p) => $q->where('project', 'like', "%{$p}%"));
        $query->when($request->search_surat_jalan, fn($q, $n) => $q->where('no_surat_jalan', 'like', "%{$n}%"));
        $query->when($request->search_barang, function ($q, $product_id) {
            return $q->whereHas('details', fn($sub) => $sub->where('product_id', $product_id));
        });

        // Ambil SEMUA data yang cocok (tanpa paginasi)
        $outgoingGoods = $query->latest()->get();

        // Data tambahan untuk ditampilkan di laporan
        $data = [
            'title' => 'Laporan Daftar Barang Keluar',
            'date' => date('d/m/Y'),
            'outgoingGoods' => $outgoingGoods
        ];

        // 2. Load view dan data, lalu generate PDF
        $pdf = PDF::loadView('outgoing_goods.report_pdf', $data);
        
        // 3. Download file PDF dengan nama spesifik
        $fileName = 'Laporan-Barang-Keluar-' . Carbon::now()->format('Y-m-d') . '.pdf';
        return $pdf->download($fileName);
    }
}