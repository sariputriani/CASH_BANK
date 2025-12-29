<?php
// namespace App\Http\Controllers;

namespace App\Http\Controllers;
use App\Models\BankMasuk;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\SubKriteria;
use App\Imports\importMasuk;
use Illuminate\Http\Request;

use App\Models\ItemSubKriteria;
use App\Models\JenisPembayaran;
use App\Models\KategoriKriteria;
use App\Imports\importExcelMasukk;
use Illuminate\Support\Facades\DB;
use App\Models\GabunganMasukKeluar;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\importExcelMasukImport;

class BankMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->keyword;
         $data = BankMasuk::select(
            'id_bank_masuk',
            'agenda_tahun',
            'tanggal',
            'id_sumber_dana',
            'id_bank_tujuan',
            'id_kategori_kriteria',
            'penerima',
            'uraian',
            'id_jenis_pembayaran',
            'nilai_rupiah',
            'debet',
            'keterangan'
        )
        ->with([
            'sumberDana:id_sumber_dana,nama_sumber_dana',
            'bankTujuan:id_bank_tujuan,nama_tujuan',
            'kategori:id_kategori_kriteria,nama_kriteria',
            'jenisPembayaran:id_jenis_pembayaran,nama_jenis_pembayaran',
        ])
       ->orderBy('tanggal', 'asc')
       ->orderBy('id_bank_masuk')
       ->paginate(25)
       ->withQueryString();

        return view('cash_bank.bankMasuk', [
            'data' => $data,
            'sumberDana' => SumberDana::all(),
            'bankTujuan' => BankTujuan::all(),
            'kategoriKriteria' => KategoriKriteria::where('tipe','Masuk')->get(),
            'jenisPembayaran' => JenisPembayaran::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'debet' => 'required|numeric',
        ]);

        BankMasuk::create([
            'agenda_tahun' => $request->agenda_tahun,
            'id_sumber_dana' => $request->id_sumber_dana,
            'id_bank_tujuan' => $request->id_bank_tujuan,
            'id_kategori_kriteria' => $request->id_kategori_kriteria,
            'id_jenis_pembayaran' => $request->id_jenis_pembayaran,
            'uraian' => $request->uraian,
            'penerima' => $request->penerima,
            'tanggal' => $request->tanggal,
            'debet' => $validated['debet'] ?? 0,
            'kredit' => 0,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success','Data berhasil disimpan');
    }

    // public function report(Request $request) {
    // /* ================= AMBIL SEMUA REQUEST FILTER ================= */
    // $tahun = $request->tahun;
    // $bulan = $request->bulan;
    // $tanggalDipilih = $request->tanggal;
    // $bankTujuanId = $request->bank_tujuan;
    // $sumberDanaIds = $request->sumber_dana;
    // $kategoriIds = $request->kategori;
    // $rekapanVA = $request->rekapanVA;
    // $idJenisPembayaran = $request->id_jenis_pembayaran;

    // /* ================= HITUNG JUMLAH FILTER AKTIF ================= */
    // $activeFilters = [];
    // $timeFilters = [];
    
    // if ($tahun) $timeFilters[] = 'tahun';
    // if ($bulan) $timeFilters[] = 'bulan';
    // if ($tanggalDipilih && count($tanggalDipilih) > 0) $timeFilters[] = 'tanggal';
    
    // if ($bankTujuanId) $activeFilters[] = 'bank_tujuan';
    // if ($sumberDanaIds && count($sumberDanaIds) > 0) $activeFilters[] = 'sumber_dana';
    // if ($kategoriIds && count($kategoriIds) > 0) $activeFilters[] = 'kategori';
    // if ($idJenisPembayaran) $activeFilters[] = 'jenis_pembayaran';
    
    // $countActiveFilters = count($activeFilters);

    // /* ================= FILTER TANGGAL (CLOSURE) ================= */
    // $filterTanggal = function ($q) use ($tahun, $bulan, $tanggalDipilih) {
    //     if (!empty($tanggalDipilih) && is_array($tanggalDipilih)) {
    //         $q->whereIn(DB::raw('DATE(tanggal)'), $tanggalDipilih);
    //     } elseif ($tahun && $bulan) {
    //         $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
    //     } elseif ($tahun) {
    //         $q->whereYear('tanggal', $tahun);
    //     }
    // };
    // $applyAllFilters = function ($q) use ($request) {

    //     if ($request->filled('tahun')) {
    //         $q->whereYear('bank_masuk.tanggal', $request->tahun);
    //     }

    //     if ($request->filled('jenis_pembayaran')) {
    //         $q->whereIn('bank_masuk.id_jenis_pembayaran', $request->jenis_pembayaran);
    //     }

    //     if ($request->filled('kategori')) {
    //         $q->whereIn('bank_masuk.id_kategori_kriteria', $request->kategori);
    //     }

    //     if ($request->filled('sumber_dana')) {
    //         $q->whereIn('bank_masuk.id_sumber_dana', $request->sumber_dana);
    //     }

    //     if ($request->filled('bankTujuan')) {
    //         $q->where('bank_masuk.id_bank_tujuan', $request->bankTujuan);
    //     }
    // };



    // $data = BankMasuk::with([
    //     'sumberDana',
    //     'bankTujuan',
    //     'kategori',
    //     'jenisPembayaran',
    // ])
    // ->where(fn($q) => $applyAllFilters($q))
    // ->orderBy('tanggal','asc')
    // ->get();

    // $bankMasuk = DB::table('bank_masuk')
    // ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_masuk.id_sumber_dana')
    // ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_masuk.id_bank_tujuan')
    // ->select(
    //     'bank_masuk.agenda_tahun',
    //     'bank_masuk.id_sumber_dana',
    //     'sumber_dana.nama_sumber_dana',
    //     'bank_masuk.id_bank_tujuan',
    //     'bank_tujuan.nama_tujuan',
    //     'bank_masuk.uraian',
    //     'bank_masuk.penerima',
    //     'bank_masuk.tanggal',
    //     'bank_masuk.debet',
    //     DB::raw('0 as kredit'),
    //     'bank_masuk.no_sap',
    //     DB::raw('NULL as nama_kriteria'),
    //     DB::raw('NULL as id_jenis_pembayaran'),
    //     DB::raw('NULL as nama_jenis_pembayaran'),
    //     DB::raw("'MASUK' as jenis"),
    //     DB::raw('bank_masuk.id_bank_masuk as urut_id')
    // )
    // ->where(fn($q) => $applyAllFilters($q))
    // ->orderBy('tanggal','asc')
    // ->get();



    // /* ================= DROPDOWN LISTS ================= */
    //  $tahunList = BankMasuk::select(DB::raw('YEAR(tanggal) as tahun'))
    //         ->groupBy(DB::raw('YEAR(tanggal)'))
    //         ->orderByDesc('tahun')
    //         ->pluck('tahun');
    //  $bulanList = BankMasuk::select(DB::raw('MONTH(tanggal) as bulan'))
    //         ->groupBy(DB::raw('MONTH(tanggal)'))
    //         ->orderBy('bulan')
    //         ->pluck('bulan');

    //  $tanggalList = BankMasuk::select(DB::raw('DATE(tanggal) as tanggal'))
    //         ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
    //         ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
    //         ->selectRaw('DATE(tanggal) as tanggal')
    //         ->pluck('tanggal');


    // $bankTujuanList = DB::table('bank_tujuan')
    //     ->where(function($query) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
    //         $query->whereExists(function($sub) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
    //             $sub->select(DB::raw(1))
    //                 ->from('bank_masuk')
    //                 ->whereColumn('bank_masuk.id_bank_tujuan', 'bank_tujuan.id_bank_tujuan')
    //                 ->where(function($q) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
    //                     $filterTanggal($q);
    //                     if ($sumberDanaIds && count($sumberDanaIds) > 0) {
    //                         $q->whereIn('id_sumber_dana', $sumberDanaIds);
    //                     }
    //                     if ($kategoriIds && count($kategoriIds) > 0) {
    //                         $q->whereIn('id_kategori_kriteria', $kategoriIds);
    //                     }
    //                     if ($idJenisPembayaran) {
    //                         $q->where('id_jenis_pembayaran', $idJenisPembayaran);
    //                     }
    //                 });
    //             });
    //         })
    //     ->orderBy('nama_tujuan')
    //     ->get();

    // $sumberDanaList = DB::table('sumber_dana')
    //     ->where(function($query) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
    //         $query->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
    //             $sub->select(DB::raw(1))
    //                 ->from('bank_masuk')
    //                 ->whereColumn('bank_masuk.id_sumber_dana', 'sumber_dana.id_sumber_dana')
    //                 ->where(function($q) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
    //                     $filterTanggal($q);
    //                     if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
    //                     if ($kategoriIds && count($kategoriIds) > 0) {
    //                         $q->whereIn('id_kategori_kriteria', $kategoriIds);
    //                     }
    //                     if ($idJenisPembayaran) $q->where('id_jenis_pembayaran', $idJenisPembayaran);
    //                 });
    //         });
    //     })
    //     ->orderBy('nama_sumber_dana')
    //     ->get();

    // $kategoriList = DB::table('kategori_kriteria')
    //     ->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $idJenisPembayaran) {
    //         $sub->select(DB::raw(1))
    //             ->from('bank_masuk')
    //             ->whereColumn('bank_masuk.id_kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria')
    //             ->where(function($q) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $idJenisPembayaran) {
    //                 $filterTanggal($q);
    //                 if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
    //                 if ($sumberDanaIds && count($sumberDanaIds) > 0) {
    //                     $q->whereIn('id_sumber_dana', $sumberDanaIds);
    //                 }
    //                 if ($idJenisPembayaran) $q->where('id_jenis_pembayaran', $idJenisPembayaran);
    //             });
    //     })
    //     ->orderBy('nama_kriteria')
    //     ->get();

    // $jenisPembayaranList = DB::table('jenis_pembayarans')
    //     ->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
    //         $sub->select(DB::raw(1))
    //             ->from('bank_masuk')
    //             ->whereColumn('bank_masuk.id_jenis_pembayaran', 'jenis_pembayarans.id_jenis_pembayaran')
    //             ->where(function($q) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
    //                 $filterTanggal($q);
    //                 if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
    //                 if ($sumberDanaIds && count($sumberDanaIds) > 0) {
    //                     $q->whereIn('id_sumber_dana', $sumberDanaIds);
    //                 }
    //                 if ($kategoriIds && count($kategoriIds) > 0) {
    //                     $q->whereIn('id_kategori_kriteria', $kategoriIds);
    //                 }
    //             });
    //     })
    //     ->orderBy('nama_jenis_pembayaran')
    //     ->get();
    // $totalDebet = $data->sum('debet');
    // /* ================= LOGIKA TAMPILAN DATA ================= */
    // $showDebet = false;
    // $showSaldoAkhir = false;
    // $showSAP = false;
    
    // if ($countActiveFilters == 0) {
    //     // Tidak ada filter (tampil semua)
    //     $showDebet = true;
    // } elseif ($countActiveFilters == 1) {
    //     // 1 filter saja (bank_tujuan, sumber_dana, atau rekapan)
    //     $showDebet = true;
    // } else {
    //     // 2 atau lebih filter = hanya kredit
    //     $showDebet = false;
    // }


    // return view('cash_bank.reportMasuk', compact(
    //     'data',
    //     'tahunList',
    //     'bulanList',
    //     'tanggalList',
    //     'bankTujuanList',
    //     'sumberDanaList',
    //     'kategoriList',
    //     'jenisPembayaranList',
    //     'showDebet',
    //     'totalDebet',
    //     'tahun',
    //     'bulan',
    //     'tanggalDipilih',
    //     'bankTujuanId',
    //     'sumberDanaIds',
    //     'kategoriIds',
    //     'idJenisPembayaran',
    //     'countActiveFilters'
    // ));
    // }
//     public function report(Request $request) {
//     /* ================= AMBIL SEMUA REQUEST FILTER ================= */
//     $tahun = $request->tahun;
//     $bulan = $request->bulan;
//     $tanggalDipilih = $request->tanggal;
//     $bankTujuanId = $request->bankTujuan; // Perhatikan nama parameter
//     $sumberDanaIds = $request->sumber_dana;
//     $kategoriIds = $request->kategori;
//     $jenisPembayaranIds = $request->jenis_pembayaran; // Tambahkan ini
//     $rekapanVA = $request->rekapanVA;

//     /* ================= HITUNG JUMLAH FILTER AKTIF ================= */
//     $activeFilters = [];
//     $timeFilters = [];
    
//     if ($tahun) $timeFilters[] = 'tahun';
//     if ($bulan) $timeFilters[] = 'bulan';
//     if ($tanggalDipilih && count($tanggalDipilih) > 0) $timeFilters[] = 'tanggal';
    
//     if ($bankTujuanId) $activeFilters[] = 'bank_tujuan';
//     if ($sumberDanaIds && count($sumberDanaIds) > 0) $activeFilters[] = 'sumber_dana';
//     if ($kategoriIds && count($kategoriIds) > 0) $activeFilters[] = 'kategori';
//     if ($jenisPembayaranIds && count($jenisPembayaranIds) > 0) $activeFilters[] = 'jenis_pembayaran';
    
//     $countActiveFilters = count($activeFilters);

//     /* ================= FILTER TANGGAL (CLOSURE) ================= */
//     $filterTanggal = function ($q) use ($tahun, $bulan, $tanggalDipilih) {
//         if (!empty($tanggalDipilih) && is_array($tanggalDipilih)) {
//             $q->whereIn(DB::raw('DATE(tanggal)'), $tanggalDipilih);
//         } elseif ($tahun && $bulan) {
//             $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
//         } elseif ($tahun) {
//             $q->whereYear('tanggal', $tahun);
//         }
//     };
//      $applyFilter = function ($q, $table = null) use (
//         $filterTanggal,
//         $bankTujuanId,
//         $sumberDanaIds,
//         $kategoriIds,
//       $idJenisPembayaran) {
//         $prefix = $table ? $table.'.' : '';
        
//         $filterTanggal($q);
        
//         if ($bankTujuanId) {
//             $q->where($prefix.'id_bank_tujuan', $bankTujuanId);
//         }
        
//         if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//             $q->whereIn($prefix.'id_sumber_dana', $sumberDanaIds);
//         }
        
//         if ($kategoriIds && is_array($kategoriIds) && count($kategoriIds) > 0) {
//             $q->whereIn($prefix.'id_kategori_kriteria', $kategoriIds);
//         }
        
//         if ($idJenisPembayaran) {
//             $q->where($prefix.'id_jenis_pembayaran', $idJenisPembayaran);
//         }
//     };

//     /* ================= QUERY DATA BANK MASUK ================= */
//     $data = BankMasuk::with([
//         'sumberDana',
//         'bankTujuan',
//         'kategori',
//         'jenisPembayaran',
//     ])
//     ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
//     ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
//     ->when($tanggalDipilih && is_array($tanggalDipilih), fn($q) => $q->whereIn(DB::raw('DATE(tanggal)'), $tanggalDipilih))
//     ->when($bankTujuanId, fn($q) => $q->where('id_bank_tujuan', $bankTujuanId))
//     ->when($sumberDanaIds && count($sumberDanaIds) > 0, fn($q) => $q->whereIn('id_sumber_dana', $sumberDanaIds))
//     ->when($kategoriIds && count($kategoriIds) > 0, fn($q) => $q->whereIn('id_kategori_kriteria', $kategoriIds))
//     ->when($jenisPembayaranIds && count($jenisPembayaranIds) > 0, fn($q) => $q->whereIn('id_jenis_pembayaran', $jenisPembayaranIds))
//     ->orderBy('tanggal', 'asc')
//     ->get();

//     /* ================= DROPDOWN LISTS ================= */
//     $tahunList = BankMasuk::select(DB::raw('YEAR(tanggal) as tahun'))
//         ->groupBy(DB::raw('YEAR(tanggal)'))
//         ->orderByDesc('tahun')
//         ->pluck('tahun');

//     $bulanList = BankMasuk::select(DB::raw('MONTH(tanggal) as bulan'))
//         ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
//         ->groupBy(DB::raw('MONTH(tanggal)'))
//         ->orderBy('bulan')
//         ->pluck('bulan');

//     $tanggalList = BankMasuk::select(DB::raw('DATE(tanggal) as tanggal'))
//         ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
//         ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
//         ->distinct()
//         ->orderBy('tanggal')
//         ->pluck('tanggal');


//     $bankTujuanList = DB::table('bank_tujuan')
//         ->where(function($query) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
//             $query->whereExists(function($sub) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
//                 $sub->select(DB::raw(1))
//                     ->from('bank_masuk')
//                     ->whereColumn('bank_masuk.id_bank_tujuan', 'bank_tujuan.id_bank_tujuan')
//                     ->where(function($q) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
//                         $filterTanggal($q);
//                         if ($sumberDanaIds && count($sumberDanaIds) > 0) {
//                             $q->whereIn('id_sumber_dana', $sumberDanaIds);
//                         }
//                         if ($kategoriIds && count($kategoriIds) > 0) {
//                             $q->whereIn('id_kategori_kriteria', $kategoriIds);
//                         }
//                         if ($idJenisPembayaran) {
//                             $q->where('id_jenis_pembayaran', $idJenisPembayaran);
//                         }
//                     });
//             });
//         })->orderBy('nama_tujuan')
//         ->get();

//     // Sumber Dana List (filtered)
//    $sumberDanaList = DB::table('sumber_dana')
//         ->where(function($query) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
//             $query->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
//                 $sub->select(DB::raw(1))
//                     ->from('bank_masuk')
//                     ->whereColumn('bank_masuk.id_sumber_dana', 'sumber_dana.id_sumber_dana')
//                     ->where(function($q) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
//                         $filterTanggal($q);
//                         if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
//                         if ($kategoriIds && count($kategoriIds) > 0) {
//                             $q->whereIn('id_kategori_kriteria', $kategoriIds);
//                         }
//                         if ($idJenisPembayaran) $q->where('id_jenis_pembayaran', $idJenisPembayaran);
//                     });
//             });
//         })->orderBy('nama_sumber_dana')
//         ->get();

//     // Kategori List (filtered)
//    $kategoriList = DB::table('kategori_kriteria')
//         ->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $idJenisPembayaran) {
//             $sub->select(DB::raw(1))
//                 ->from('bank_masuk')
//                 ->whereColumn('bank_masuk.id_kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria')
//                 ->where(function($q) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $idJenisPembayaran) {
//                     $filterTanggal($q);
//                     if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
//                     if ($sumberDanaIds && count($sumberDanaIds) > 0) {
//                         $q->whereIn('id_sumber_dana', $sumberDanaIds);
//                     }
//                     if ($idJenisPembayaran) $q->where('id_jenis_pembayaran', $idJenisPembayaran);
//                 });
//         })
//         ->orderBy('nama_kriteria')
//         ->get();

//     // Jenis Pembayaran List (filtered)
//     $jenisPembayaranList = DB::table('jenis_pembayarans')
//         ->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
//             $sub->select(DB::raw(1))
//                 ->from('bank_masuk')
//                 ->whereColumn('bank_masuk.id_jenis_pembayaran', 'jenis_pembayarans.id_jenis_pembayaran')
//                 ->where(function($q) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
//                     $filterTanggal($q);
//                     if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
//                     if ($sumberDanaIds && count($sumberDanaIds) > 0) {
//                         $q->whereIn('id_sumber_dana', $sumberDanaIds);
//                     }
//                     if ($kategoriIds && count($kategoriIds) > 0) {
//                         $q->whereIn('id_kategori_kriteria', $kategoriIds);
//                     }
//                 });
//         })
//         ->orderBy('nama_jenis_pembayaran')
//         ->get();

//     $totalDebet = $data->sum('debet');

//     /* ================= LOGIKA TAMPILAN DATA ================= */
//     $showDebet = false;
    
//     if ($countActiveFilters == 0) {
//         $showDebet = true;
//     } elseif ($countActiveFilters == 1) {
//         $showDebet = true;
//     } else {
//         $showDebet = false;
//     }

//     return view('cash_bank.reportMasuk', compact(
//         'data',
//         'tahunList',
//         'bulanList',
//         'tanggalList',
//         'bankTujuanList',
//         'sumberDanaList',
//         'kategoriList',
//         'jenisPembayaranList',
//         'showDebet',
//         'totalDebet',
//         'tahun',
//         'bulan',
//         'tanggalDipilih',
//         'bankTujuanId',
//         'sumberDanaIds',
//         'kategoriIds',
//         'jenisPembayaranIds',
//         'idJenisPembayaran',
//         'countActiveFilters'
//     ));
// }

public function report(Request $request)
{
    /* ================= REQUEST ================= */
    $tahun               = $request->tahun;
    $bulan               = $request->bulan;
    $bankTujuanId        = $request->bankTujuan;          // SINGLE
    $sumberDanaIds       = $request->sumber_dana ?? [];   // ARRAY
    $kategoriIds         = $request->kategori ?? [];      // ARRAY
    $jenisPembayaranIds  = $request->jenis_pembayaran ?? []; // ARRAY

    /* ================= QUERY DATA ================= */
    $data = BankMasuk::with(['sumberDana','bankTujuan','kategori','jenisPembayaran'])
        ->when($tahun, fn ($q) => $q->whereYear('tanggal', $tahun))
        ->when($bulan, fn ($q) => $q->whereMonth('tanggal', $bulan))
        ->when($bankTujuanId, fn ($q) => $q->where('id_bank_tujuan', $bankTujuanId))
        ->when(count($sumberDanaIds), fn ($q) => $q->whereIn('id_sumber_dana', $sumberDanaIds))
        ->when(count($kategoriIds), fn ($q) => $q->whereIn('id_kategori_kriteria', $kategoriIds))
        ->when(count($jenisPembayaranIds), fn ($q) => $q->whereIn('id_jenis_pembayaran', $jenisPembayaranIds))
        ->orderBy('tanggal')
        ->get();

    /* ================= DROPDOWN TERHUBUNG ================= */

    // Bank Tujuan
    $bankTujuanList = DB::table('bank_tujuan')
        ->whereExists(function ($q) use ($tahun,$bulan,$sumberDanaIds,$kategoriIds,$jenisPembayaranIds) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_bank_tujuan','bank_tujuan.id_bank_tujuan')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when(count($sumberDanaIds), fn($x)=>$x->whereIn('id_sumber_dana',$sumberDanaIds))
              ->when(count($kategoriIds), fn($x)=>$x->whereIn('id_kategori_kriteria',$kategoriIds))
              ->when(count($jenisPembayaranIds), fn($x)=>$x->whereIn('id_jenis_pembayaran',$jenisPembayaranIds));
        })
        ->orderBy('nama_tujuan')
        ->get();

    // Sumber Dana
    $sumberDanaList = DB::table('sumber_dana')
        ->whereExists(function ($q) use ($tahun,$bulan,$bankTujuanId,$kategoriIds,$jenisPembayaranIds) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_sumber_dana','sumber_dana.id_sumber_dana')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when($bankTujuanId, fn($x)=>$x->where('id_bank_tujuan',$bankTujuanId))
              ->when(count($kategoriIds), fn($x)=>$x->whereIn('id_kategori_kriteria',$kategoriIds))
              ->when(count($jenisPembayaranIds), fn($x)=>$x->whereIn('id_jenis_pembayaran',$jenisPembayaranIds));
        })
        ->orderBy('nama_sumber_dana')
        ->get();

    // Kategori
    $kategoriList = DB::table('kategori_kriteria')
        ->whereExists(function ($q) use ($tahun,$bulan,$bankTujuanId,$sumberDanaIds,$jenisPembayaranIds) {
            $q->select(DB::raw(1))
              ->from('bank_masuk')
              ->whereColumn('bank_masuk.id_kategori_kriteria','kategori_kriteria.id_kategori_kriteria')
              ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
              ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
              ->when($bankTujuanId, fn($x)=>$x->where('id_bank_tujuan',$bankTujuanId))
              ->when(count($sumberDanaIds), fn($x)=>$x->whereIn('id_sumber_dana',$sumberDanaIds))
              ->when(count($jenisPembayaranIds), fn($x)=>$x->whereIn('id_jenis_pembayaran',$jenisPembayaranIds));
        })
        ->orderBy('nama_kriteria')
        ->get();

    // Jenis Pembayaran
    // $jenisPembayaranList = DB::table('jenis_pembayarans')
    //     ->whereExists(function ($q) use ($tahun,$bulan,$bankTujuanId,$sumberDanaIds,$kategoriIds) {
    //         $q->select(DB::raw(1))
    //           ->from('bank_masuk')
    //           ->whereColumn('bank_masuk.id_jenis_pembayaran','jenis_pembayarans.id_jenis_pembayaran')
    //           ->when($tahun, fn($x)=>$x->whereYear('tanggal',$tahun))
    //           ->when($bulan, fn($x)=>$x->whereMonth('tanggal',$bulan))
    //           ->when($bankTujuanId, fn($x)=>$x->where('id_bank_tujuan',$bankTujuanId))
    //           ->when(count($sumberDanaIds), fn($x)=>$x->whereIn('id_sumber_dana',$sumberDanaIds))
    //           ->when(count($kategoriIds), fn($x)=>$x->whereIn('id_kategori_kriteria',$kategoriIds));
    //     })
    //     ->orderBy('nama_jenis_pembayaran')
    //     ->get();
     $jenisPembayaranList = DB::table('jenis_pembayarans')
        ->whereExists(function($sub) use ($tahun,$bulan, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
            $sub->select(DB::raw(1))
                ->from('bank_masuk')
                ->whereColumn('bank_masuk.id_jenis_pembayaran', 'jenis_pembayarans.id_jenis_pembayaran')
                ->where(function($q) use ($tahun,$bulan, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
                    if ($tahun) $q->whereYear('tanggal', $tahun);
                    if ($bulan) $q->whereMonth('tanggal', $bulan);
                    if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
                    if ($sumberDanaIds && count($sumberDanaIds) > 0) {
                        $q->whereIn('id_sumber_dana', $sumberDanaIds);
                    }
                    if ($kategoriIds && count($kategoriIds) > 0) {
                        $q->whereIn('id_kategori_kriteria', $kategoriIds);
                    }
                });
        })
        ->orderBy('nama_jenis_pembayaran')
        ->get();

    $tahunList = BankMasuk::selectRaw('YEAR(tanggal) tahun')->groupBy('tahun')->pluck('tahun');

    return view('cash_bank.reportMasuk', compact(
        'data',
        'tahunList',
        'bankTujuanList',
        'sumberDanaList',
        'kategoriList',
        'jenisPembayaranList'
    ));
}


    public function importExcel(Request $request)
    {
        // $request->validate([
        //     'fileExcel' => 'required|mimes:xlsx,xls'
        // ]);

        // ini_set('memory_limit', '-1');
        // set_time_limit(0);

        // // Excel::import(new EmployeeImport, $request->file('fileExcel'));
        // Excel::queueImport(
        // new EmployeeImport,
        // $request->file('fileExcel')
        $request->validate([
            'fileExcel' => 'required|mimes:xlsx,xls'
        ]);
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $file = $request->file('fileExcel')->store('public/import');

        Excel::import(new importMasuk, $file);

        return redirect()
            ->route('bank-masuk.index')
            ->with('success', 'Data berhasil diimport');
            }

    public function edit(string $id)
    {
        $masuk = BankMasuk::findOrFail($id);
        return view('cash_bank.modal.edit', compact('masuk'));
    }
    public function update(Request $request, string $id)
    {

        $masuk = BankMasuk::findOrFail($id);
        $masuk->update($request->all());

        return redirect()->route('bank-masuk.index')->with('success', 'Data berhasil diperbarui');
    }

      public function destroy(string $id)
    {
        $data = BankMasuk::findOrFail($id);
        $data->delete();

        return redirect()->route('bank-masuk.index')->with('success', 'Data berhasil dihapus');
    }
}