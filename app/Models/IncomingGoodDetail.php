<?php

namespace App\Models; // <-- PASTIKAN baris ini benar

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingGoodDetail extends Model // <-- PASTIKAN nama kelas ini benar
{
    use HasFactory;
    
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'incoming_good_details';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'incoming_good_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relasi ke model IncomingGood.
     */
    public function incomingGood()
    {
        return $this->belongsTo(IncomingGood::class);
    }

    /**
     * Relasi ke model Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}