<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasSlug;

    /**
     * Atribut yang dapat diisi secara massal.
     * PASTIKAN 'code' ADA DI DALAM ARRAY INI.
     *
     * @var array
     */
    protected $fillable = [
        'code', // <-- INI YANG PALING PENTING
        'category_id',
        'supplier_id',
        'name',
        'slug',
        'description',
        'quantity',
        'image',
        'unit',
    ];

    /**
     * Accessor untuk atribut 'image'.
     * Mengubah path gambar menjadi URL yang lengkap.
     */
    public function getImageAttribute($value)
    {
        // Pengecekan asset() untuk memastikan path digenerate dengan benar
        if ($value && file_exists(storage_path('app/public/' . $value))) {
            return asset('storage/' . $value);
        }
        
        // Fallback jika tidak ada gambar atau file tidak ditemukan
        return 'https://fakeimg.pl/308x205/?text=Product&font=lexend';
    }

    /**
     * Relasi ke model Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke model Supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke model Cart.
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }

    /**
     * Relasi ke detail barang masuk.
     * Sebuah produk bisa memiliki banyak catatan barang masuk.
     */
    public function incomingDetails()
    {
        return $this->hasMany(IncomingGoodDetail::class);
    }

    /**
     * Relasi ke detail barang keluar.
     * Sebuah produk bisa memiliki banyak catatan barang keluar.
     */
    public function outgoingDetails()
    {
        return $this->hasMany(OutgoingGoodDetail::class);
    }
}