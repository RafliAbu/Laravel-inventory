<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingGood extends Model
{
    use HasFactory;

    protected $table = 'outgoing_goods';

    protected $fillable = [
        'tanggal',
        'project',
        'no_surat_jalan',
        'do_number',
        'jo_number',
        'to_number',
    ];

    /**
     * Relasi ke detail barang keluar.
     */
    public function details()
    {
        return $this->hasMany(OutgoingGoodDetail::class);
    }
}