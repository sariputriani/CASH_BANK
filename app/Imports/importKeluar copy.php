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
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class importKeluar implements ToModel, WithHeadingRow, WithChunkReading, IValueBinder
{
    use Importable;

    public function chunkSize(): int
    {
        return 1000;
    }



       // 🔥 PAKSA SEMUA ISI EXCEL DIBACA STRING (ANTI NUMERIC)
    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
        return true;
    }

    private function cleanText($text)
    {
        return trim(
            preg_replace('/\s+/', ' ',
                str_replace(["\r", "\n"], ' ', $text)
            )
        );
    }

    
    private function parseTanggal($val)
    {
        if ($val === null || $val === '') return null;

        // JIKA EXCEL SUDAH JADI NUMERIC
        if (is_numeric($val)) {
            $dt = \Carbon\Carbon::instance(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $val)
            );

            // 🔥 PAKSA BALIK: anggap Excel salah bulan
            return \Carbon\Carbon::create(
                $dt->year,
                $dt->day,   // bulan = hari
                $dt->month  // hari = bulan
            )->format('Y-m-d');
        }

        // STRING (3/1/2025, 3-1-2025, dst)
        $val = str_replace(['-', '.'], '/', trim($val));

        return \Carbon\Carbon::createFromFormat('d/m/Y', $val)
            ->format('Y-m-d');
    }

    public function model(array $row)
    {
        $sumberDana     = $this->cleanText($row['sumber_dana'] ?? '');
        $bankTujuan     = $this->cleanText($row['bank_tujuan'] ?? '');
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
            // dd($row['tanggal']),

            'id_sumber_dana' => $sumberDana ? SumberDana::where('nama_sumber_dana', 'LIKE', "%{$sumberDana}%")
                ->value('id_sumber_dana') : null,

            'id_bank_tujuan' => $bankTujuan !== ''
                ? BankTujuan::where('nama_tujuan', 'LIKE', "%{$bankTujuan}%")->value('id_bank_tujuan')
                : null,

            'id_kategori_kriteria' => $kategori !== '' 
            ? KategoriKriteria::where('nama_kriteria', 'LIKE', "%{$kategori}%")->value('id_kategori_kriteria') 
            : null,

            'id_sub_kriteria' =>$subKategori !== '' ? SubKriteria::where('nama_sub_kriteria', 'LIKE', "%{$subKategori}%")->value('id_sub_kriteria') 
                : null,

            'id_item_sub_kriteria' =>$itemSubKriteria !== '' ? ItemSubKriteria::where('nama_item_sub_kriteria', 'LIKE', "%{$itemSubKriteria}%")->value('id_item_sub_kriteria') 
                : null,

            'id_jenis_pembayaran' =>$jenisPembayaran !== '' ? JenisPembayaran::where('nama_jenis_pembayaran', 'LIKE', "%{$jenisPembayaran}%")->value('id_jenis_pembayaran') 
                : null,

            'penerima' => $row['penerima'] ?? null,
            'uraian'   => $row['uraian'] ?? null,

            'kredit'        => $kredit,
            'debet'         => 0,
            'nilai_rupiah'  => $kredit,
        ]);
    }
}
