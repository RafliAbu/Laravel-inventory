<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Traits\HasImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HasImage;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $searchProduct = $request->input('search_product');
        // Ubah nama variabel agar lebih jelas
        $supplierId = $request->input('supplier_id'); 

        // --- TAMBAHAN BARU ---
        // Ambil semua supplier untuk ditampilkan di dropdown filter
        $suppliers = Supplier::orderBy('name')->get();

        $query = Product::with(['supplier', 'category'])->latest();

        if ($searchProduct) {
            $query->where('name', 'like', "%{$searchProduct}%");
        }

        // --- PERUBAHAN LOGIKA FILTER ---
        // Filter berdasarkan ID supplier yang dipilih, bukan nama
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        if ($perPage == -1) {
            $total = $query->count();
            $products = $query->paginate($total > 0 ? $total : 1);
        } else {
            $products = $query->paginate($perPage);
        }

        // --- KIRIM DATA SUPPLIERS KE VIEW ---
        return view('admin.product.index', compact('products', 'suppliers'));
    }

    // ... (method lainnya tidak perlu diubah) ...
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $categories = Category::withCount('products')->get();

        return view('admin.product.create', compact('suppliers', 'categories'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $image = $this->uploadImage($request, $path = 'public/products/', $name = 'image');

        Product::create([
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'code' => $request->code,
            'name' => $request->name,
            'image' => $image->hashName(),
            'unit' => $request->unit,
            'description' => $request->description,
        ]);

        return redirect(route('admin.product.index'))->with('toast_success', 'Barang Berhasil Ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $suppliers = Supplier::get();
        $categories = Category::get();

        return view('admin.product.edit', compact('product', 'suppliers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $image = $this->uploadImage($request, $path = 'public/products/', $name = 'image');

        $product->update([
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'code' => $request->code,
            'name' => $request->name,
            'unit' => $request->unit,
            'description' => $request->description,
        ]);

        if($request->file($name)){
            $this->updateImage(
                $path = 'public/products/', $name = 'image', $data = $product, $url = $image->hashName()
            );
        }

        return redirect(route('admin.product.index'))->with('toast_success', 'Produk Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        Storage::disk('local')->delete('public/products/'. basename($product->image));

        return back()->with('toast_success', 'Barang Berhasil Dihapus');
    }
}