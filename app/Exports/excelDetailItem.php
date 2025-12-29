<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Fromview;

class excelDetailItem implements Fromview
{
    /**
    * @return \Illuminate\Support\view
    */

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function view() : View
    {
        $request = $this->request;
        $kategori = urldecode($request->kategori);
        $sub      = urldecode($request->sub);
        $item     = urldecode($request->item);
        $tahun    = $request->tahun;

    if (!$kategori || !$sub || !$item || !$tahun) {
        return redirect()->route('bank-keluar.report')
            ->with('error', 'Parameter tidak lengkap.');
    }

    // ================= BANK MASUK =================
    $bankMasuk = DB::table('bank_masuk')
        ->selectRaw("
            bank_masuk.tanggal,
            bank_masuk.agenda_tahun,
            bank_masuk.id_kategori_kriteria,
            bank_masuk.id_sub_kriteria,
            bank_masuk.id_item_sub_kriteria,
            bank_masuk.id_bank_tujuan,
            bank_masuk.id_sumber_dana,
            bank_masuk.id_jenis_pembayaran,
            bank_masuk.penerima,
            bank_masuk.uraian,
            bank_masuk.kredit,
            'MASUK' as sumber
        ")
        ->whereYear('bank_masuk.tanggal', $tahun)
        ->where('bank_masuk.kredit', '>', 0);

    // Filter bulan jika ada
    if ($request->filled('bulan') && $request->bulan != 'Semua Jenis Bulan') {
        $bankMasuk->whereMonth('bank_masuk.tanggal', $request->bulan);
    }

    // Filter tanggal (range) jika ada
    if ($request->filled('tanggal') && is_array($request->tanggal) && count($request->tanggal) == 2) {
        $bankMasuk->whereBetween('bank_masuk.tanggal', [
            $request->tanggal[0], 
            $request->tanggal[1]
        ]);
    }

    // Filter bank tujuan jika ada
    if ($request->filled('bank_tujuan') && is_numeric($request->bank_tujuan)) {
        $bankMasuk->where('bank_masuk.id_bank_tujuan', $request->bank_tujuan);
    }

    // Filter sumber dana jika ada (bisa multiple)
    if ($request->filled('sumber_dana')) {
        if (is_array($request->sumber_dana)) {
            $bankMasuk->whereIn('bank_masuk.id_sumber_dana', $request->sumber_dana);
        } elseif (is_numeric($request->sumber_dana)) {
            $bankMasuk->where('bank_masuk.id_sumber_dana', $request->sumber_dana);
        }
    }

    // Filter jenis pembayaran jika ada
    if ($request->filled('id_jenis_pembayaran') && is_numeric($request->id_jenis_pembayaran)) {
        $bankMasuk->where('bank_masuk.id_jenis_pembayaran', $request->id_jenis_pembayaran);
    }

    // ================= BANK KELUAR =================
    $bankKeluar = DB::table('bank_keluars')
        ->selectRaw("
            bank_keluars.tanggal,
            bank_keluars.agenda_tahun,
            bank_keluars.id_kategori_kriteria,
            bank_keluars.id_sub_kriteria,
            bank_keluars.id_item_sub_kriteria,
            bank_keluars.id_bank_tujuan,
            bank_keluars.id_sumber_dana,
            bank_keluars.id_jenis_pembayaran,
            bank_keluars.penerima,
            bank_keluars.uraian,
            bank_keluars.kredit,
            'KELUAR' as sumber
        ")
        ->whereYear('bank_keluars.tanggal', $tahun)
        ->where('bank_keluars.kredit', '>', 0);

    // Filter bulan jika ada
    if ($request->filled('bulan') && $request->bulan != 'Semua Jenis Bulan') {
        $bankKeluar->whereMonth('bank_keluars.tanggal', $request->bulan);
    }

    // Filter tanggal (range) jika ada
    if ($request->filled('tanggal') && is_array($request->tanggal) && count($request->tanggal) == 2) {
        $bankKeluar->whereBetween('bank_keluars.tanggal', [
            $request->tanggal[0], 
            $request->tanggal[1]
        ]);
    }

    // Filter bank tujuan jika ada
    if ($request->filled('bank_tujuan') && is_numeric($request->bank_tujuan)) {
        $bankKeluar->where('bank_keluars.id_bank_tujuan', $request->bank_tujuan);
    }

    // Filter sumber dana jika ada (bisa multiple)
    if ($request->filled('sumber_dana')) {
        if (is_array($request->sumber_dana)) {
            $bankKeluar->whereIn('bank_keluars.id_sumber_dana', $request->sumber_dana);
        } elseif (is_numeric($request->sumber_dana)) {
            $bankKeluar->where('bank_keluars.id_sumber_dana', $request->sumber_dana);
        }
    }

    // Filter jenis pembayaran jika ada
    if ($request->filled('id_jenis_pembayaran') && is_numeric($request->id_jenis_pembayaran)) {
        $bankKeluar->where('bank_keluars.id_jenis_pembayaran', $request->id_jenis_pembayaran);
    }

    // ================= UNION =================
    $data = DB::query()
        ->fromSub(
            $bankMasuk->unionAll($bankKeluar),
            'trx'
        )
        ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'trx.id_kategori_kriteria')
        ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'trx.id_sub_kriteria')
        ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'trx.id_item_sub_kriteria')
        ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'trx.id_bank_tujuan')
        ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'trx.id_sumber_dana')
        ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'trx.id_jenis_pembayaran')
        ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
        ->where('sub_kriteria.nama_sub_kriteria', 'LIKE', "%{$sub}%")
        ->where('item_sub_kriteria.nama_item_sub_kriteria', 'LIKE', "%{$item}%")
        ->orderBy('trx.tanggal', 'desc')
        ->get();

    $totalKredit = $data->sum('kredit');

    // ================= INFO FILTER =================
    $filterInfo = [];
    
    if ($request->filled('bulan') && $request->bulan != 'Semua Jenis Bulan') {
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $filterInfo[] = 'Bulan: ' . ($bulanNama[$request->bulan] ?? $request->bulan);
    }
    
    // Filter info tanggal (range)
    if ($request->filled('tanggal') && is_array($request->tanggal) && count($request->tanggal) == 2) {
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal[0])->format('d/m/Y');
        $tanggalAkhir = \Carbon\Carbon::parse($request->tanggal[1])->format('d/m/Y');
        $filterInfo[] = 'Periode: ' . $tanggalMulai . ' - ' . $tanggalAkhir;
    }
    
    // Filter info bank tujuan
    if ($request->filled('bank_tujuan') && is_numeric($request->bank_tujuan)) {
        $bankTujuanNama = DB::table('bank_tujuan')
            ->where('id_bank_tujuan', $request->bank_tujuan)
            ->value('nama_tujuan');
        if ($bankTujuanNama) {
            $filterInfo[] = 'Bank: ' . $bankTujuanNama;
        }
    }
    
    // Filter info sumber dana
    if ($request->filled('sumber_dana')) {
        $sumberDanaIds = is_array($request->sumber_dana) ? $request->sumber_dana : [$request->sumber_dana];
        $sumberDanaNama = DB::table('sumber_dana')
            ->whereIn('id_sumber_dana', $sumberDanaIds)
            ->pluck('nama_sumber_dana')
            ->toArray();
        if (!empty($sumberDanaNama)) {
            $filterInfo[] = 'Sumber Dana: ' . implode(', ', $sumberDanaNama);
        }
    }
    
    // Filter info jenis pembayaran
    if ($request->filled('id_jenis_pembayaran') && is_numeric($request->id_jenis_pembayaran)) {
        $jenisPembayaranNama = DB::table('jenis_pembayarans')
            ->where('id_jenis_pembayaran', $request->id_jenis_pembayaran)
            ->value('nama_jenis_pembayaran');
        if ($jenisPembayaranNama) {
            $filterInfo[] = 'Jenis Pembayaran: ' . $jenisPembayaranNama;
        }
    }

    // ================= AMBIL TANGGAL YANG TERSEDIA =================
    // Query untuk mendapatkan tanggal yang tersedia berdasarkan filter yang sudah diterapkan
    $availableDates = collect();
    
    // Build query untuk bank_masuk
    $queryMasuk = DB::table('bank_masuk')
        ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_masuk.id_kategori_kriteria')
        ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_masuk.id_sub_kriteria')
        ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_masuk.id_item_sub_kriteria')
        ->select(DB::raw('DISTINCT DATE(bank_masuk.tanggal) as tanggal'))
        ->whereYear('bank_masuk.tanggal', $tahun)
        ->where('bank_masuk.kredit', '>', 0)
        ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
        ->where('sub_kriteria.nama_sub_kriteria', 'LIKE', "%{$sub}%")
        ->where('item_sub_kriteria.nama_item_sub_kriteria', 'LIKE', "%{$item}%");

    // Build query untuk bank_keluar
    $queryKeluar = DB::table('bank_keluars')
        ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
        ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
        ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
        ->select(DB::raw('DISTINCT DATE(bank_keluars.tanggal) as tanggal'))
        ->whereYear('bank_keluars.tanggal', $tahun)
        ->where('bank_keluars.kredit', '>', 0)
        ->where('kategori_kriteria.nama_kriteria', 'LIKE', "%{$kategori}%")
        ->where('sub_kriteria.nama_sub_kriteria', 'LIKE', "%{$sub}%")
        ->where('item_sub_kriteria.nama_item_sub_kriteria', 'LIKE', "%{$item}%");

    // Terapkan filter yang sama seperti query utama
    if ($request->filled('bulan') && $request->bulan != 'Semua Jenis Bulan') {
        $queryMasuk->whereMonth('bank_masuk.tanggal', $request->bulan);
        $queryKeluar->whereMonth('bank_keluars.tanggal', $request->bulan);
    }

    if ($request->filled('bank_tujuan') && is_numeric($request->bank_tujuan)) {
        $queryMasuk->where('bank_masuk.id_bank_tujuan', $request->bank_tujuan);
        $queryKeluar->where('bank_keluars.id_bank_tujuan', $request->bank_tujuan);
    }

    if ($request->filled('sumber_dana')) {
        if (is_array($request->sumber_dana)) {
            $queryMasuk->whereIn('bank_masuk.id_sumber_dana', $request->sumber_dana);
            $queryKeluar->whereIn('bank_keluars.id_sumber_dana', $request->sumber_dana);
        } elseif (is_numeric($request->sumber_dana)) {
            $queryMasuk->where('bank_masuk.id_sumber_dana', $request->sumber_dana);
            $queryKeluar->where('bank_keluars.id_sumber_dana', $request->sumber_dana);
        }
    }

    if ($request->filled('id_jenis_pembayaran') && is_numeric($request->id_jenis_pembayaran)) {
        $queryMasuk->where('bank_masuk.id_jenis_pembayaran', $request->id_jenis_pembayaran);
        $queryKeluar->where('bank_keluars.id_jenis_pembayaran', $request->id_jenis_pembayaran);
    }

    // Gabungkan hasil dari kedua query
    $availableDates = $queryMasuk->union($queryKeluar)
        ->orderBy('tanggal', 'asc')
        ->pluck('tanggal')
        ->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        })
        ->unique()
        ->values();

    return view('cash_bank.exportExcel.detailItem', compact(
        'data',
        'kategori',
        'sub',
        'item',
        'tahun',
        'totalKredit',
        'filterInfo',
        'availableDates'
    ));
    }
}
