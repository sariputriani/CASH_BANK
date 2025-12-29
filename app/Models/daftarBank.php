<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class daftarBank extends Model
{
    use HasFactory;
    protected $table = 'daftarbanks';

    protected $fillable = [
      'nama_bank'
    ];

    public $timestamps = true;
}

