<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingGoodDetail extends Model
{
    use HasFactory;

    protected $table = 'outgoing_good_details';

    protected $fillable = [
        'outgoing_good_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relasi ke transaksi utama.
     */
    public function outgoingGood()
    {
        return $this->belongsTo(OutgoingGood::class);
    }

    /**
     * Relasi ke produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}