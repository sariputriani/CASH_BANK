<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GabunganMasukKeluar;
use Illuminate\Support\Facades\DB;

class DetailKategoriController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter dari URL (decode karena mungkin ada URL encoding)
        $kategori = urldecode($request->kategori);
        $tahun = $request->tahun;

        // Validasi parameter
        if (!$kategori || !$tahun) {
            return redirect()->route('bank-keluar.report')
                ->with('error', 'Parameter tidak lengkap. Silakan pilih kategori dari report.');
        }

        try {
            // Query untuk mendapatkan semua transaksi dalam kategori
            $data = GabunganMasukKeluar::select(
                    'gabungan_masuk_keluars.*',
                    'kategori_kriteria.nama_kriteria',
                    'sub_kriteria.nama_sub_kriteria',
                    'item_sub_kriteria.nama_item_sub_kriteria',
                    'bank_tujuan.nama_tujuan',
                    'sumber_dana.nama_sumber_dana'
                )
                ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
                ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'gabungan_masuk_keluars.id_sub_kriteria')
                ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'gabungan_masuk_keluars.id_item_sub_kriteria')
                ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'gabungan_masuk_keluars.id_bank_tujuan')
                ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'gabungan_masuk_keluars.id_sumber_dana')
                ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
                ->where('gabungan_masuk_keluars.kredit', '>', 0);

            // Filter tahun
            if (DB::getSchemaBuilder()->hasColumn('gabungan_masuk_keluars', 'tanggal')) {
                $data->whereYear('gabungan_masuk_keluars.tanggal', $tahun);
            } elseif (DB::getSchemaBuilder()->hasColumn('gabungan_masuk_keluars', 'tanggal_keluar')) {
                $data->whereYear('gabungan_masuk_keluars.tanggal_keluar', $tahun);
            }

            $data = $data->orderBy('sub_kriteria.nama_sub_kriteria', 'asc')
                         ->orderBy('item_sub_kriteria.nama_item_sub_kriteria', 'asc')
                         ->orderBy('gabungan_masuk_keluars.tanggal', 'asc')
                         ->get();

            // Debugging
            \Log::info('Detail Kategori Query', [
                'kategori' => $kategori,
                'tahun' => $tahun,
                'result_count' => $data->count(),
                'total_kredit' => $data->sum('kredit')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error Query Detail Kategori: ' . $e->getMessage());
            
            // Fallback query
            $data = GabunganMasukKeluar::with(['kategori', 'subKriteria', 'itemSubKriteria', 'bankTujuan', 'sumberDana'])
                ->whereHas('kategori', function($q) use ($kategori) {
                    $q->where('nama_kriteria', 'LIKE', "%{$kategori}%");
                })
                ->where('kredit', '>', 0)
                ->whereYear('tanggal', $tahun)
                ->get();
        }

        // Hitung total kredit
        $totalKredit = $data->sum('kredit');

        return view('cash_bank.detail.detailKategori', [
            'data' => $data,
            'kategori' => $kategori,
            'tahun' => $tahun,
            'totalKredit' => $totalKredit
        ]);
    }

    public function export(Request $request)
    {
        $kategori = urldecode($request->kategori);
        $tahun = $request->tahun;

        $data = GabunganMasukKeluar::select(
                'gabungan_masuk_keluars.*',
                'kategori_kriteria.nama_kriteria',
                'sub_kriteria.nama_sub_kriteria',
                'item_sub_kriteria.nama_item_sub_kriteria',
                'bank_tujuan.nama_tujuan',
                'sumber_dana.nama_sumber_dana'
            )
            ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
            ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'gabungan_masuk_keluars.id_sub_kriteria')
            ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'gabungan_masuk_keluars.id_item_sub_kriteria')
            ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'gabungan_masuk_keluars.id_bank_tujuan')
            ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'gabungan_masuk_keluars.id_sumber_dana')
            ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
            ->where('gabungan_masuk_keluars.kredit', '>', 0)
            ->whereYear('gabungan_masuk_keluars.tanggal', $tahun)
            ->orderBy('sub_kriteria.nama_sub_kriteria', 'asc')
            ->orderBy('item_sub_kriteria.nama_item_sub_kriteria', 'asc')
            ->get();

        $totalKredit = $data->sum('kredit');

        $filename = 'detail_kategori_' . str_replace(' ', '_', $kategori) . '_' . $tahun . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data, $kategori, $tahun, $totalKredit) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['DETAIL TRANSAKSI KATEGORI']);
            fputcsv($file, ['Kategori:', $kategori]);
            fputcsv($file, ['Tahun:', $tahun]);
            fputcsv($file, []);
            
            fputcsv($file, [
                'No', 'Tanggal', 'Agenda', 'Kategori', 'Sub Kategori', 
                'Item', 'Bank Tujuan', 'Sumber Dana', 'Penerima', 'Uraian', 'Kredit'
            ]);

            foreach ($data as $index => $row) {
                $tanggal = $row->tanggal ?? $row->tanggal_keluar ?? $row->created_at;
                
                fputcsv($file, [
                    $index + 1,
                    \Carbon\Carbon::parse($tanggal)->format('d/m/Y'),
                    $row->agenda_tahun ?? '-',
                    $row->nama_kriteria ?? '-',
                    $row->nama_sub_kriteria ?? '-',
                    $row->nama_item_sub_kriteria ?? '-',
                    $row->nama_tujuan ?? '-',
                    $row->nama_sumber_dana ?? '-',
                    $row->penerima ?? '-',
                    $row->uraian ?? '-',
                    number_format($row->kredit, 0, ',', '.')
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', '', '', '', '', 'TOTAL:', number_format($totalKredit, 0, ',', '.')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}