<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\BankKeluar;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\KategoriKriteria;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeeImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected $sumberDanaMap;
    protected $bankTujuanMap;
    protected $kategoriMap;

    public function __construct()
    {
        $this->sumberDanaMap = SumberDana::pluck('id_sumber_dana', 'nama_sumber_dana')
            ->mapWithKeys(fn($v,$k)=>[strtolower(trim($k))=>$v]);

        $this->bankTujuanMap = BankTujuan::pluck('id_bank_tujuan', 'nama_tujuan')
            ->mapWithKeys(fn($v,$k)=>[strtolower(trim($k))=>$v]);

        $this->kategoriMap = KategoriKriteria::pluck('id_kategori_kriteria', 'nama_kriteria')
            ->mapWithKeys(fn($v,$k)=>[strtolower(trim($k))=>$v]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function key($text)
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $text)));
    }

    public function model(array $row)
    {
        $sumberDana = $this->key($row['sumber_dana'] ?? '');
        $bankTujuan = $this->key($row['bank_tujuan'] ?? '');
        $kategori   = $this->key($row['kategori'] ?? '');

        $kredit = str_replace(['.', ','], '', $row['kredit'] ?? 0);

        return new BankKeluar([
            'agenda_tahun' => $row['agenda_tahun'] ?? null,
            'tanggal' => !empty($row['tanggal'])
                ? (is_numeric($row['tanggal'])
                    ? Carbon::instance(Date::excelToDateTimeObject($row['tanggal']))->format('Y-m-d')
                    : Carbon::parse(str_replace('/', '-', $row['tanggal']))->format('Y-m-d')
                )
                : null,

            'id_sumber_dana' => $this->sumberDanaMap[$sumberDana] ?? null,
            'id_bank_tujuan' => $this->bankTujuanMap[$bankTujuan] ?? null,
            'id_kategori_kriteria' => $this->kategoriMap[$kategori] ?? null,

            'penerima' => $row['penerima'] ?? null,
            'uraian' => $row['uraian'] ?? null,
            'kredit' => is_numeric($kredit) ? $kredit : 0,
            'nilai_rupiah' => is_numeric($kredit) ? $kredit : 0,
            'debet' => 0,
            'id_jenis_pembayaran' => $row['jenis_pembayaran'] ?? null,
        ]);
    }
}


