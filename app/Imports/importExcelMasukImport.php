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
        return 1000; 
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
    Log::info('IMPORT ROW', $row); // DEBUG

    $sumberDana = $this->cleanText($row['sumber_dana'] ?? '');
    $bankTujuan = $this->cleanText($row['bank_tujuan'] ?? '');
    $kategori   = $this->cleanText($row['kategori'] ?? '');

    $debetRaw = $row['debet'] ?? 0;
    $debet = (int) str_replace(['.', ','], '', $debetRaw);

    return new BankMasuk([
        'agenda_tahun' => $row['agenda_tahun'] ?? null,

        'tanggal' => !empty($row['tanggal'])
            ? (is_numeric($row['tanggal'])
                ? Carbon::instance(Date::excelToDateTimeObject($row['tanggal']))->format('Y-m-d')
                : Carbon::parse(str_replace('/', '-', $row['tanggal']))->format('Y-m-d')
            )
            : null,

        'id_sumber_dana' => SumberDana::where(
            'nama_sumber_dana',
            'LIKE',
            "%$sumberDana%"
        )->value('id_sumber_dana'),

        'id_bank_tujuan' => BankTujuan::where(
            'nama_tujuan',
            'LIKE',
            "%$bankTujuan%"
        )->value('id_bank_tujuan'),

        'id_kategori_kriteria' => KategoriKriteria::where(
            'nama_kriteria',
            'LIKE',
            "%$kategori%"
        )->value('id_kategori_kriteria'),

        'penerima' => $row['penerima'] ?? null,
        'uraian' => $row['uraian'] ?? null,

        'debet' => $debet,
        'nilai_rupiah' => $debet,
        'kredit' => 0,

        'jenis_pembayaran' => $row['jenis_pembayaran'] ?? null,
    ]);
}

}
