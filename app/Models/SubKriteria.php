<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    protected $table = 'sub_kriteria';
    protected $primaryKey = 'id_sub_kriteria';
    protected $fillable = ['id_kategori_kriteria', 'nama_sub_kriteria'];

    public function itemSub()
    {
        return $this->hasMany(ItemSubKriteria::class, 'id_sub_kriteria');
    }
}
