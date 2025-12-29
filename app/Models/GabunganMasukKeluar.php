<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GabunganMasukKeluar extends Model
{
    use HasFactory;

    protected $fillable =[
        'nomor_agenda',
        'id_sumber_dana',
        'id_bank_tujuan',
        'id_kategori_kriteria',
        'id_sub_kriteria',
        'id_item_sub_kriteria',
        'dokumen_id',
        'agenda_tahun',
        'uraian',
        'jenis_pembayaran',
        'nilai_rupiah',
        'penerima',
        'tanggal',
        'debet',
        'kredit',
        'keterangan',
        'jenis'
    ];
    
    public $timestamps = true;

    // Relasi ke dokumen (wajib)
    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'dokumen_id');
    }

    // Relasi lainnya
    public function sumberDana()
    {
        return $this->belongsTo(SumberDana::class, 'id_sumber_dana');
    }

    public function bankTujuan()
    {
        return $this->belongsTo(BankTujuan::class, 'id_bank_tujuan');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriKriteria::class, 'id_kategori_kriteria');
    }

    public function subKriteria()
    {
        return $this->belongsTo(SubKriteria::class, 'id_sub_kriteria');
    }

    public function itemSubKriteria()
    {
        return $this->belongsTo(ItemSubKriteria::class, 'id_item_sub_kriteria');
    }

}