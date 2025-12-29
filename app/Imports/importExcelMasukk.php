<?php

namespace App\Imports;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\BankMasuk;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\SubKriteria;
use App\Models\ItemSubKriteria;
use App\Models\KategoriKriteria;
use Maatwebsite\Excel\Concerns\ToModel;

class importExcelMasukk implements ToModel
{
    private function cleanText($text)
    {
        return trim(
            preg_replace('/\s+/', ' ',
                str_replace(["\r", "\n"], ' ', $text)
            )
        );
    }

    public function model(array $row)
    {
        // ambil text dulu
        $sumberDana = $this->cleanText($row[2] ?? '');
        $bankTujuan = $this->cleanText($row[3] ?? '');
        $kategori   = $this->cleanText($row[4] ?? '');

        // convert tanggal
        $tanggal = is_numeric($row[1])
            ? Carbon::instance(Date::excelToDateTimeObject($row[1]))
            : Carbon::createFromFormat('d/m/Y', trim($row[1]));

        // debet
        $debet = str_replace(['.', ','], '', $row[9] ?? 0);
        $debet = is_numeric($debet) ? (int)$debet : 0;

        return new BankMasuk([
            'agenda_tahun' => $row[0] ?? null,
            'tanggal' => $tanggal,

            'id_sumber_dana' => SumberDana::where('nama_sumber_dana','LIKE',"%{$sumberDana}%")
                ->value('id_sumber_dana'),

            'id_bank_tujuan' => BankTujuan::where('nama_tujuan','LIKE',"%{$bankTujuan}%")
                ->value('id_bank_tujuan'),

            'id_kategori_kriteria' => KategoriKriteria::where('nama_kriteria','LIKE',"%{$kategori}%")
                ->value('id_kategori_kriteria'),

            'penerima' => $row[5] ?? null,
            'uraian' => $row[6] ?? null,

            'debet' => $debet,
            'nilai_rupiah' => $debet,
            'kredit' => 0,

            'jenis_pembayaran' => $row[10] ?? null,
        ]);
    }
}
