<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\bankKeluar;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\SubKriteria;
use App\Models\ItemSubKriteria;
use App\Models\JenisPembayaran;
use App\Models\KategoriKriteria;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas; // ✅ UNTUK RUMUS

class importKeluar implements ToModel, WithHeadingRow, WithChunkReading, WithCalculatedFormulas
{
    use Importable;

    public function chunkSize(): int
    {
        return 1000;
    }

    private function cleanText($text)
    {
        if ($text === null || $text === '') return '';
        
        // Force ke string untuk handle scientific notation
        $text = (string) $text;
        
        return trim(
            preg_replace('/\s+/', ' ',
                str_replace(["\r", "\n"], ' ', $text)
            )
        );
    }

   private function parseTanggal($val)
{
    if ($val === null || $val === '') return null;
    if (is_numeric($val) && $val > 1000) {
        try {
            $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $val);
            $year = $dt->format('Y');
            $month = $dt->format('m');
            $day = $dt->format('d');   
            return sprintf('%s-%s-%s', $year, $day, $month);
            
        } catch (\Exception $e) {
            return null;
        }
    }
    $val = str_replace(['-', '.'], '/', trim((string)$val));
    
    // Validasi format
    if (!preg_match('#^\d{1,2}/\d{1,2}/\d{4}$#', $val)) {
        return null;
    }
    try {
        return \Carbon\Carbon::createFromFormat('d/m/Y', $val)->format('Y-m-d');
    } catch (\Exception $e) {
        return null;
    }
}

    public function model(array $row)
    {
    //     \Log::info('Row data:', [
    //     'bank_tujuan_raw' => $row['bank_tujuan'] ?? 'NULL',
    //     'bank_tujuan_type' => gettype($row['bank_tujuan'] ?? null),
    //     'all_row' => $row
    // ]);
    
      
        $sumberDana     = $this->cleanText($row['sumber_dana'] ?? '');
        $bankTujuan     = $this->cleanText($row['bank_tujuan'] ?? ''); // ← Hasil rumus
        $kategori       = $this->cleanText($row['kategori'] ?? '');
        $subKategori    = $this->cleanText($row['sub_kriteria'] ?? '');
        $itemSubKriteria= $this->cleanText($row['item_sub_kriteria'] ?? '');
        $jenisPembayaran= $this->cleanText($row['jenis_pembayaran'] ?? '');

        $kredit = isset($row['kredit'])
            ? (int) str_replace(['.', ','], '', $row['kredit'])
            : 0;

        return new bankKeluar([
            'agenda_tahun' => $row['agenda_tahun'] ?? null,
            'tanggal'      => $this->parseTanggal($row['tanggal'] ?? null),

            'id_sumber_dana' => $sumberDana ? SumberDana::where('nama_sumber_dana', 'LIKE', "%{$sumberDana}%")
                ->value('id_sumber_dana') : null,

            'id_bank_tujuan' => $bankTujuan !== ''
                ? BankTujuan::where('nama_tujuan', 'LIKE', "%{$bankTujuan}%")->value('id_bank_tujuan')
                : null,

            'id_kategori_kriteria' => $kategori !== '' 
                ? KategoriKriteria::where('nama_kriteria', 'LIKE', "%{$kategori}%")->value('id_kategori_kriteria') 
                : null,

            'id_sub_kriteria' => $subKategori !== '' 
                ? SubKriteria::where('nama_sub_kriteria', 'LIKE', "%{$subKategori}%")->value('id_sub_kriteria') 
                : null,

            'id_item_sub_kriteria' => $itemSubKriteria !== '' 
                ? ItemSubKriteria::where('nama_item_sub_kriteria', 'LIKE', "%{$itemSubKriteria}%")->value('id_item_sub_kriteria') 
                : null,

            'id_jenis_pembayaran' => $jenisPembayaran !== '' 
                ? JenisPembayaran::where('nama_jenis_pembayaran', 'LIKE', "%{$jenisPembayaran}%")->value('id_jenis_pembayaran') 
                : null,

            'penerima' => $row['penerima'] ?? null,
            'uraian'   => $row['uraian'] ?? null,

            'kredit'        => $kredit,
            'debet'         => 0,
            'nilai_rupiah'  => $kredit,
        ]);
    }
}