<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKriteria extends Model
{
    protected $table = 'kategori_kriteria';
    protected $primaryKey = 'id_kategori_kriteria';
    protected $fillable = ['nama_kriteria'];

    public function subKriteria()
    {
        return $this->hasMany(SubKriteria::class, 'id_kategori_kriteria');
    }
}
