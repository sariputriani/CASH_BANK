<?php

namespace App\Exports;

use App\Models\BankMasuk;
use illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportExcelBankMasuk implements FromView

{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = BankMasuk::select(
            'id_bank_masuk',
            'agenda_tahun',
            'tanggal',
            'id_sumber_dana',
            'id_bank_tujuan',
            'id_kategori_kriteria',
            'penerima',
            'uraian',
            'id_jenis_pembayaran',
            'nilai_rupiah',
            'debet',
            'keterangan'
        )
        ->with([
            'sumberDana:id_sumber_dana,nama_sumber_dana',
            'bankTujuan:id_bank_tujuan,nama_tujuan',
            'kategori:id_kategori_kriteria,nama_kriteria',
            'jenisPembayaran:id_jenis_pembayaran,nama_jenis_pembayaran',
        ])
       ->orderBy('tanggal', 'asc')
       ->orderBy('id_bank_masuk')
       ->get();

       return view('cash_bank.exportExcel.excelMasuk', compact('data'));
    }
}
