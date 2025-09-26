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
     */
    protected $fillable = [
        'code',
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
     * The attributes that should be cast.
     * Memastikan tipe data selalu konsisten.
     */
    protected $casts = [
        'quantity' => 'integer',
        'category_id' => 'integer',
        'supplier_id' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     * Method ini akan dieksekusi saat model diinisialisasi.
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Event 'creating' ini berjalan otomatis SETIAP KALI
         * sebuah produk baru akan disimpan ke database.
         */
        static::creating(function ($product) {
            // Jika 'code' tidak diisi manual, buat secara otomatis.
            if (is_null($product->code)) {
                $latestProduct = static::latest('id')->first();

                if (!$latestProduct) {
                    $nextNumber = 1;
                } else {
                    // Ekstrak nomor dari kode terakhir (misal: BRG-009 -> 9)
                    $lastNumber = (int) substr($latestProduct->code, 4);
                    $nextNumber = $lastNumber + 1;
                }

                // Format nomor dengan 3 digit (misal: 1 -> 001, 10 -> 010)
                $product->code = 'BRG-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Mengubah path gambar menjadi URL yang lengkap.
     */
    public function getImageAttribute($value)
    {
        if ($value && file_exists(storage_path('app/public/' . $value))) {
            return asset('storage/' . $value);
        }
        
        // Fallback jika tidak ada gambar
        return 'https://fakeimg.pl/308x205/?text=Product&font=lexend';
    }
    
    /**
     * Memberitahu Laravel untuk menggunakan 'slug'
     * pada Route Model Binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }

    public function incomingDetails()
    {
        return $this->hasMany(IncomingGoodDetail::class);
    }

    public function outgoingDetails()
    {
        return $this->hasMany(OutgoingGoodDetail::class);
    }
}