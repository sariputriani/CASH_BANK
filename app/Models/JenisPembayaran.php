<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    protected $table = 'jenis_pembayarans';
    protected $primaryKey = 'id_jenis_pembayaran';
    protected $fillable = [
        'nama_jenis_pembayaran'
    ];
}
