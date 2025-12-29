<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GabunganMasukKeluar;

class DetailSubKategoriController extends Controller
{
    public function index(Request $request)
    {
        dd([
        'message' => '✅ Controller BERHASIL dipanggil!',
        'kategori_raw' => $request->kategori,
        'sub_raw' => $request->sub,
        'item_raw' => $request->item,
        'tahun_raw' => $request->tahun,
        'all_params' => $request->all(),
        'query_string' => $request->getQueryString(),
        'full_url' => $request->fullUrl()
    ]);
        $kategori = urldecode($request->kategori);
        $sub = urldecode($request->sub);
        $item = urldecode($request->item);
        $tahun = $request->tahun;


        if (!$kategori || !$sub || !$tahun) {
            return redirect()->route('bank-keluar.report')
                ->with('error', 'Parameter tidak lengkap. Silakan pilih item dari report.');
        }


        $isAllItems = ($item === 'ALL' || $item === 'all' || empty($item));

        try {

            $query = GabunganMasukKeluar::select(
                    'gabungan_masuk_keluars.*',
                    'kategori_kriteria.nama_kriteria',
                    'sub_kriteria.nama_sub_kriteria',
                    'item_sub_kriteria.nama_item_sub_kriteria',
                    'bank_tujuan.nama_tujuan'
                )
                ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
                ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'gabungan_masuk_keluars.id_sub_kriteria')
                ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'gabungan_masuk_keluars.id_item_sub_kriteria')
                ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'gabungan_masuk_keluars.id_bank_tujuan')
                ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
                ->where('sub_kriteria.nama_sub_kriteria', 'LIKE', "%{$sub}%")
                ->where('gabungan_masuk_keluars.kredit', '>', 0);


            if (!$isAllItems) {
                $query->where('item_sub_kriteria.nama_item_sub_kriteria', 'LIKE', "%{$item}%");
            }

            if (DB::getSchemaBuilder()->hasColumn('gabungan_masuk_keluars', 'tanggal')) {
                $query->whereYear('gabungan_masuk_keluars.tanggal', $tahun);
            } elseif (DB::getSchemaBuilder()->hasColumn('gabungan_masuk_keluars', 'tanggal_keluar')) {
                $query->whereYear('gabungan_masuk_keluars.tanggal_keluar', $tahun);
            }

            $data = $query->orderBy('item_sub_kriteria.nama_item_sub_kriteria', 'asc')
                          ->orderBy('gabungan_masuk_keluars.tanggal', 'asc')
                          ->get();


            \Log::info('Detail Item Query', [
                'kategori' => $kategori,
                'sub' => $sub,
                'item' => $item,
                'isAllItems' => $isAllItems,
                'tahun' => $tahun,
                'result_count' => $data->count(),
                'total_kredit' => $data->sum('kredit')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error Query Detail Item: ' . $e->getMessage());
            

            $query = GabunganMasukKeluar::with(['kategori', 'subKriteria', 'itemSubKriteria', 'bankTujuan'])
                ->whereHas('kategori', function($q) use ($kategori) {
                    $q->where('nama_kriteria', 'LIKE', "%{$kategori}%");
                })
                ->whereHas('subKriteria', function($q) use ($sub) {
                    $q->where('nama_sub_kriteria', 'LIKE', "%{$sub}%");
                })
                ->where('kredit', '>', 0)
                ->whereYear('created_at', $tahun);

            if (!$isAllItems) {
                $query->whereHas('itemSubKriteria', function($q) use ($item) {
                    $q->where('nama_item_sub_kriteria', 'LIKE', "%{$item}%");
                });
            }

            $data = $query->get();
        }


        $totalKredit = $data->sum('kredit');


        $displayItem = $isAllItems ? "Semua Item dalam {$sub}" : $item;

        return view('cash_bank.detail.detailItem', [
            'data' => $data,
            'kategori' => $kategori,
            'sub' => $sub,
            'item' => $displayItem,
            'itemOriginal' => $item, // untuk export
            'tahun' => $tahun,
            'totalKredit' => $totalKredit,
            'isAllItems' => $isAllItems
        ]);
    }

    public function export(Request $request)
    {
        $kategori = urldecode($request->kategori);
        $sub = urldecode($request->sub);
        $item = urldecode($request->item);
        $tahun = $request->tahun;

        $data = GabunganMasukKeluar::select(
                'gabungan_masuk_keluars.*',
                'kategori_kriteria.nama_kriteria',
                'sub_kriteria.nama_sub_kriteria',
                'item_sub_kriteria.nama_item_sub_kriteria',
                'bank_tujuan.nama_tujuan'
            )
            ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
            ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'gabungan_masuk_keluars.id_sub_kriteria')
            ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'gabungan_masuk_keluars.id_item_sub_kriteria')
            ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'gabungan_masuk_keluars.id_bank_tujuan')
            ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
            ->where('sub_kriteria.nama_sub_kriteria', 'LIKE', "%{$sub}%")
            ->where('item_sub_kriteria.nama_item_sub_kriteria', 'LIKE', "%{$item}%")
            ->where('gabungan_masuk_keluars.kredit', '>', 0)
            ->whereYear('gabungan_masuk_keluars.tanggal', $tahun)
            ->get();

        $totalKredit = $data->sum('kredit');

        $filename = 'detail_' . str_replace(' ', '_', $item) . '_' . $tahun . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data, $kategori, $sub, $item, $tahun, $totalKredit) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['DETAIL TRANSAKSI']);
            fputcsv($file, ['Kategori:', $kategori]);
            fputcsv($file, ['Sub Kriteria:', $sub]);
            fputcsv($file, ['Item:', $item]);
            fputcsv($file, ['Tahun:', $tahun]);
            fputcsv($file, []);
            
            fputcsv($file, [
                'No', 'Tanggal', 'Agenda', 'Kategori', 'Sub Kategori', 
                'Item', 'Bank Tujuan', 'Penerima', 'Uraian', 'Kredit'
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
                    $row->penerima ?? '-',
                    $row->uraian ?? '-',
                    number_format($row->kredit, 0, ',', '.')
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', '', '', '', 'TOTAL:', number_format($totalKredit, 0, ',', '.')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
