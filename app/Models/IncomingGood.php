<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingGood extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'incoming_goods';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'tanggal',
        'supplier_id',
        'no_surat_jalan',
        'rkm_po',
        'po',
        'project',
    ];

    /**
     * Relasi ke model Supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke model IncomingGoodDetail.
     */
    public function details()
    {
        return $this->hasMany(IncomingGoodDetail::class);
    }
}