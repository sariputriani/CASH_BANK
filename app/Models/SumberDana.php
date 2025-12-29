<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    protected $table = 'sumber_dana';
    protected $primaryKey = 'id_sumber_dana';
    protected $fillable = ['nama_dana'];
}
