<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\bankKeluar;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\SubKriteria;
use App\Models\ItemSubKriteria;
// use App\Models\GabunganMasukKeluar;
use App\Models\KategoriKriteria;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
// use Maatwebsite\Excel\Concerns\ShouldQueue;
// use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeeImport implements ToModel,ShouldQueue, WithChunkReading, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function chunkSize(): int
    {
        return 500; // proses 500 baris sekali
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
        // dd($row);
        return new bankKeluar([
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
            $sub        = $this->cleanText($row[5]),
            $item       = $this->cleanText($row[6]),

            'id_sumber_dana' => SumberDana::where('nama_sumber_dana', 'LIKE', "%$sumberDana%")
            ->value('id_sumber_dana'),

            'id_bank_tujuan' => BankTujuan::where('nama_tujuan', 'LIKE', "%$bankTujuan%")
            ->value('id_bank_tujuan'),

            'id_kategori_kriteria' => KategoriKriteria::where('nama_kriteria', 'LIKE', "%$kategori%")
            ->value('id_kategori_kriteria'),

            'id_sub_kriteria' => SubKriteria::where('nama_sub_kriteria', 'LIKE', "%$sub%")
            ->value('id_sub_kriteria'),

            'id_item_sub_kriteria' => ItemSubKriteria::where('nama_item_sub_kriteria', 'LIKE', "%$item%")
            ->value('id_item_sub_kriteria'),

            // dd($row[3]),
            
            'penerima' => $row[7],
            'uraian' => $row[8],
            
            $kredit = str_replace(['.', ','], '', $row[9]),
            'kredit' => is_numeric($kredit) ? $kredit : 0,
            'nilai_rupiah' => is_numeric($kredit) ? $kredit : 0,
            
            'debet' => 0, // WAJIB 0 untuk bank keluar
            
            // 'nilai_rupiah' =>  0,
            'jenis_pembayaran' => $row[10] ?? null,
]);

    //         // A
    //         'tanggal' => is_numeric($row[0])
    // ? Carbon::instance(Date::excelToDateTimeObject($row[0]))
    // : Carbon::createFromFormat('d/m/Y', trim($row[0])),

    //         // B
    //         'nomor_agenda' => $row[1] ?? 0,

    //         // C
    //         'agenda_tahun' => $row[2] ?? null,

    //         // D
    //         'id_sumber_dana' => SumberDana::where('nama_sumber_dana', trim($row[3]))->value('id_sumber_dana') ?? null,

    //         // E
    //         'id_bank_tujuan' => BankTujuan::where('nama_tujuan', trim($row[4]))->value('id_bank_tujuan') ?? null,

    //         // F
    //         'id_kategori_kriteria' => KategoriKriteria::where('nama_kriteria', trim($row[5]))->value('id_kategori_kriteria') ?? null,

    //         // G
    //         'id_sub_kriteria' => SubKriteria::where('nama_sub_kriteria', trim($row[6]))->value('id_sub_kriteria') ?? null,

    //         // H
    //         'id_item_sub_kriteria' => ItemSubKriteria::where('nama_item_sub_kriteria', trim($row[7]))->value('id_item_sub_kriteria') ?? null,

    //         // I
    //         'penerima' => $row[8] ?? null,

    //         // J
    //         'uraian' => $row[9] ?? null,

    //         // K
    //         'nilai_rupiah' => $row[10] ?? 0,

    //         // L
    //         'debet' => 0,

    //         // M
    //         'kredit' => $row[11] ?? 0,

    //         // N
    //         'jenis' => 'Keluar',

    //         // O
    //         'keterangan' => $row[12] ?? null,

    //         // P
    //         'jenis_pembayaran' => $row[13] ?? null,
    //     ]);
    }
}
