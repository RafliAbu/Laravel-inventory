<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * Menggunakan Enum untuk casting kolom status.
     * Ini adalah praktik yang sangat baik.
     */
    protected $casts = [
        'status' => OrderStatus::class
    ];

    /**
     * Mematikan mass assignment protection.
     * Pastikan Anda sudah melakukan validasi di controller.
     */
    protected $guarded = [];
    
    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendefinisikan relasi "belongsTo" ke model User.
     * Satu Order dibuat oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * REVISI UTAMA: Menambahkan relasi "belongsTo" ke model Product.
     * Ini akan memperbaiki error "undefined relationship".
     * Satu Order merujuk pada satu Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * REVISI: Accessor untuk atribut 'image' dibuat lebih pintar.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Prioritas 1: Ambil gambar dari produk yang terhubung (jika ada).
                // Model Product sudah punya accessor sendiri, jadi kita tinggal panggil.
                if ($this->product) {
                    return $this->product->image;
                }

                // Prioritas 2 (Cadangan): Jika tidak ada produk terhubung,
                // gunakan gambar dari kolom 'image' di tabel order ini (untuk data lama).
                if ($value) {
                    return asset('storage/orders/' . $value);
                }
                
                // Prioritas 3: Gambar pengganti jika tidak ada sama sekali.
                return 'https://fakeimg.pl/308x205/?text=Item&font=lexend';
            }
        );
    }
}