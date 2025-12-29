<?php

namespace App\Exports;

use App\Models\BankMasuk;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class reportMasukExcel implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function view() :View
    {
          /* ================= REQUEST ================= */
    $tahun = $this->request->tahun;
        $bulan = $this->request->bulan;
        $bankTujuanId = $this->request->bankTujuan;
        $sumberDanaIds = $this->request->sumber_dana ?? [];
        $kategoriIds = $this->request->kategori ?? [];
        $jenisPembayaranId = $this->request->jenis_pembayaran;

    /* ================= QUERY DATA ================= */
    $data = BankMasuk::with(['sumberDana','bankTujuan','kategori','jenisPembayaran'])
        ->when($tahun, fn ($q) => $q->whereYear('tanggal', $tahun))
        ->when($bulan, fn ($q) => $q->whereMonth('tanggal', $bulan))
        ->when($bankTujuanId, fn ($q) => $q->where('id_bank_tujuan', $bankTujuanId))
        ->when($jenisPembayaranId, fn ($q) => $q->where('id_jenis_pembayaran', $jenisPembayaranId))
        ->when(count($sumberDanaIds), fn ($q) => $q->whereIn('id_sumber_dana', $sumberDanaIds))
        ->when(count($kategoriIds), fn ($q) => $q->whereIn('id_kategori_kriteria', $kategoriIds))
        ->orderBy('tanggal')
        ->get();

    /* ================= DROPDOWN TERHUBUNG ================= */

    // Bank Tujuan
    $bankTujuanList = DB::table('bank_tujuan')
        ->whereExists(function ($q) use ($tahun,$bulan,$sumberDanaIds,$kategoriIds,$jenisPembayaranId) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_bank_tujuan','bank_tujuan.id_bank_tujuan')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when(count($sumberDanaIds), fn($x)=>$x->whereIn('id_sumber_dana',$sumberDanaIds))
              ->when(count($kategoriIds), fn($x)=>$x->whereIn('id_kategori_kriteria',$kategoriIds))
              ->when($jenisPembayaranId, fn($x)=>$x->where('id_jenis_pembayaran', $jenisPembayaranId));
        })
        ->orderBy('nama_tujuan')
        ->get();

    // Sumber Dana
    $sumberDanaList = DB::table('sumber_dana')
        ->whereExists(function ($q) use ($tahun,$bulan,$bankTujuanId,$kategoriIds,$jenisPembayaranId) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_sumber_dana','sumber_dana.id_sumber_dana')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when($bankTujuanId, fn($x)=>$x->where('id_bank_tujuan',$bankTujuanId))
              ->when($jenisPembayaranId, fn($x)=>$x->where('id_jenis_pembayaran', $jenisPembayaranId))
              ->when(count($kategoriIds), fn($x)=>$x->whereIn('id_kategori_kriteria',$kategoriIds));
        })
        ->orderBy('nama_sumber_dana')
        ->get();

    // Kategori
    $kategoriList = DB::table('kategori_kriteria')
        ->whereExists(function ($q) use ($tahun,$bulan,$bankTujuanId,$sumberDanaIds,$jenisPembayaranId) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_kategori_kriteria','kategori_kriteria.id_kategori_kriteria')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when($bankTujuanId, fn($x)=>$x->where('id_bank_tujuan',$bankTujuanId))
              ->when($jenisPembayaranId, fn($x)=>$x->where('id_jenis_pembayaran', $jenisPembayaranId))
              ->when(count($sumberDanaIds), fn($x)=>$x->whereIn('id_sumber_dana',$sumberDanaIds));

        })
        ->orderBy('nama_kriteria')
        ->get();

    // Jenis Pembayaran
    $jenisPembayaranList = DB::table('jenis_pembayarans')
        ->whereExists(function ($q) use ($tahun,$bulan,$bankTujuanId,$sumberDanaIds,$kategoriIds) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_jenis_pembayaran','jenis_pembayarans.id_jenis_pembayaran')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when($bankTujuanId, fn($x)=>$x->where('id_bank_tujuan',$bankTujuanId))
              ->when(count($sumberDanaIds), fn($x)=>$x->whereIn('id_sumber_dana',$sumberDanaIds))
              ->when(count($kategoriIds), fn($x)=>$x->whereIn('id_kategori_kriteria',$kategoriIds));
        })
        ->orderBy('nama_jenis_pembayaran')
        ->get();

    $tahunList = BankMasuk::selectRaw('YEAR(tanggal) tahun')->groupBy('tahun')->pluck('tahun');

    return view('cash_bank.exportExcel.excelReportMasuk', compact(
        'data',
        'tahunList',
        'bankTujuanList',
        'sumberDanaList',
        'kategoriList',
        'jenisPembayaranList'
    ));
    }
}
