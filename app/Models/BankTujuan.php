<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTujuan extends Model
{
    protected $table = 'bank_tujuan';
    protected $primaryKey = 'id_bank_tujuan';
    protected $fillable = ['nama_tujuan'];
}