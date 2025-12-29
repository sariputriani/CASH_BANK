<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\BankMasuk;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\JenisPembayaran;
use App\Models\KategoriKriteria;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class importExcelMasukImport implements ToModel,ShouldQueue, WithChunkReading, WithHeadingRow
{
    public function chunkSize(): int
    {
        return 1000; // ideal
    }
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
        dd($row);
        return new BankMasuk([
            'agenda_tahun' => $row[0],
            
           'tanggal' => !empty($row[1])
                ? (is_numeric($row[1])
                    ? Carbon::instance(Date::excelToDateTimeObject($row[1]))->format('Y-m-d')
                    : Carbon::parse(str_replace('/', '-', trim($row[1])))->format('Y-m-d')
                )
                : null,
            
            $sumberDana = $this->cleanText($row[2]),
            $bankTujuan = $this->cleanText($row[3]),
            $kategori   = $this->cleanText($row[4]),
            // $sub        = $this->cleanText($row[5]),
            // $item       = $this->cleanText($row[6]),

            'id_sumber_dana' => SumberDana::where('nama_sumber_dana', 'LIKE', "%$sumberDana%")
            ->value('id_sumber_dana'),

            'id_bank_tujuan' => BankTujuan::where('nama_tujuan', 'LIKE', "%$bankTujuan%")
            ->value('id_bank_tujuan'),

            'id_kategori_kriteria' => KategoriKriteria::where('nama_kriteria', 'LIKE', "%$kategori%")
            ->value('id_kategori_kriteria'),

            // 'id_sub_kriteria' => SubKriteria::where('nama_sub_kriteria', 'LIKE', "%$sub%")
            // ->value('id_sub_kriteria'),

            // 'id_item_sub_kriteria' => ItemSubKriteria::where('nama_item_sub_kriteria', 'LIKE', "%$item%")
            // ->value('id_item_sub_kriteria'),

            // dd($row[3]),
            
            'penerima' => $row[6],
            'uraian' => $row[7],
            
            $debet = str_replace(['.', ','], '', $row[8]),
            'debet' => is_numeric($debet) ? $debet : 0,
            'nilai_rupiah' => is_numeric($debet) ? $debet : 0,
            
            'kredit' => 0, // WAJIB 0 untuk bank keluar
            
            // 'nilai_rupiah' =>  0,
            'jenis_pembayaran' => $row[9] ?? null,
]);
    }
}
