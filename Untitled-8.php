<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Dokumen;
use App\Models\BankKeluar;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use App\Imports\importKeluar;
use App\Models\DokumenAgenda;
use App\Imports\EmployeeImport;
use App\Models\ItemSubKriteria;
use App\Models\JenisPembayaran;
use App\Models\KategoriKriteria;
use Illuminate\Support\Facades\DB;
use App\Models\GabunganMasukKeluar;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
// use Maatwebsite\Excel\Excel;

class BankKeluarController extends Controller
{
   
    public function index(Request $request)
    {
        /* ================= DATA UTAMA (PAGINATION + EAGER LOADING) ================= */
    $data = BankKeluar::select(
            'id_bank_keluar',
            'agenda_tahun',
            'tanggal',
            'id_sumber_dana',
            'id_bank_tujuan',
            'id_kategori_kriteria',
            'id_sub_kriteria',
            'id_item_sub_kriteria',
            'penerima',
            'uraian',
            'id_jenis_pembayaran',
            'nilai_rupiah',
            'kredit',
            'keterangan'
        )
        ->with([
            'sumberDana:id_sumber_dana,nama_sumber_dana',
            'bankTujuan:id_bank_tujuan,nama_tujuan',
            'kategori:id_kategori_kriteria,nama_kriteria',
            'subKriteria:id_sub_kriteria,nama_sub_kriteria',
            'itemSubKriteria:id_item_sub_kriteria,nama_item_sub_kriteria',
            'jenisPembayaran:id_jenis_pembayaran,nama_jenis_pembayaran',
        ])
       ->orderBy('tanggal', 'asc')
       ->orderBy('id_bank_keluar')
        ->paginate(25)
        ->withQueryString();

    /* ================= DATA AGENDA (TETAP) ================= */
    $agenda = DB::connection('mysql_agenda_online')
        ->table('dokumens')
        ->select(
            'id as dokumen_id',
            DB::raw("CONCAT(nomor_agenda,'_',tahun) as agenda_tahun"),
            'uraian_spp as uraian',
            'nilai_rupiah',
            'dibayar_kepada as penerima',
            'jenis_pembayaran'
        )
        ->where('status_pembayaran', 'SIAP DIBAYAR')
        ->get();

    /* ================= CACHE DATA MASTER ================= */
    $sumberDana = Cache::remember('sumber_dana', 3600, fn () => SumberDana::all());
    $bankTujuan = Cache::remember('bank_tujuan', 3600, fn () => BankTujuan::all());
    $kategoriKriteria = Cache::remember(
        'kategori_keluar',
        3600,
        fn () => KategoriKriteria::where('tipe', 'Keluar')->get()
    );
    $subKriteria = Cache::remember('sub_kriteria', 3600, fn () => SubKriteria::all());
    $itemSubKriteria = Cache::remember('item_sub_kriteria', 3600, fn () => ItemSubKriteria::all());
    $jenisPembayaran = Cache::remember('jenis_pembayaran', 3600, fn () => JenisPembayaran::all());

    return view('cash_bank.bankKeluar', compact(
        'data',
        'agenda',
        'sumberDana',
        'bankTujuan',
        'kategoriKriteria',
        'subKriteria',
        'itemSubKriteria',
        'jenisPembayaran'
    ));
    }


    public function store(Request $request)
{
    $validated = $request->validate([
        'agenda_tahun'           => 'nullable|string',
        'id_sumber_dana'         => 'nullable|exists:sumber_dana,id_sumber_dana',
        'id_bank_tujuan'         => 'nullable|exists:bank_tujuan,id_bank_tujuan',
        'id_kategori_kriteria'   => 'nullable|exists:kategori_kriteria,id_kategori_kriteria',
        'id_sub_kriteria'        => 'nullable|exists:sub_kriteria,id_sub_kriteria',
        'id_item_sub_kriteria'   => 'nullable|exists:item_sub_kriteria,id_item_sub_kriteria',
        'uraian'                 => 'nullable|string',
        'jenis_pembayaran'       => 'nullable|string',
        'nilai_rupiah'           => 'nullable|numeric|min:0',
        'penerima'               => 'nullable|string',
        'tanggal'                => 'nullable|date',
        'debet'                  => 'nullable|numeric|min:0',
        'kredit'                 => 'nullable|numeric|min:0',
        'keterangan'             => 'nullable|string',
    ]);

    $validated['debet']  = $validated['debet'] ?? 0;
    $validated['kredit'] = $validated['kredit'] ?? 0;

    DB::beginTransaction();

    $input        = $request->agenda_tahun;
    $dokumen_id   = null;
    $no_agenda    = null;
    $agenda_tahun = $input;

    if (is_numeric($input)) {
        $dokumen = DB::connection('mysql_agenda_online')
            ->table('dokumens')
            ->find($input);

        if ($dokumen) {
            $dokumen_id   = $dokumen->id;
            $no_agenda    = $dokumen->nomor_agenda;
            $agenda_tahun = $dokumen->nomor_agenda . '_' . $dokumen->tahun;

            DB::connection('mysql_agenda_online')
                ->table('dokumens')
                ->where('id', $dokumen->id)
                ->update([
                    'uraian_spp'        => $request->uraian,
                    'nilai_rupiah'      => $request->nilai_rupiah,
                    'dibayar'           => $request->nilai_rupiah,
                    'dibayar_kepada'    => $request->penerima,
                    'status_pembayaran' => 'SUDAH DIBAYAR',
                    'tanggal_dibayar'   => $request->tanggal,
                ]);
        }
    }

    BankKeluar::create([
        'dokumen_id'            => $dokumen_id,
        'no_agenda'             => $no_agenda,
        'agenda_tahun'          => $agenda_tahun,
        'id_sumber_dana'        => $request->id_sumber_dana,
        'id_bank_tujuan'        => $request->id_bank_tujuan,
        'id_kategori_kriteria'  => $request->id_kategori_kriteria,
        'id_sub_kriteria'       => $request->id_sub_kriteria,
        'id_item_sub_kriteria'  => $request->id_item_sub_kriteria,
        'uraian'                => $request->uraian,
        'nilai_rupiah'          => $request->nilai_rupiah ?? 0,
        'penerima'              => $request->penerima,
        'tanggal'               => $request->tanggal,
        'id_jenis_pembayaran'   => $request->id_jenis_pembayaran,
        'debet'                 => $validated['debet'],
        'kredit'                => $validated['kredit'],
        'keterangan'            => $request->keterangan,
    ]);

    DB::commit();

    return redirect()->back()->with('success', 'Data Bank Keluar berhasil disimpan');
}


   public function getSub($id)
    {
        return SubKriteria::where('id_kategori_kriteria', $id)->get();
    }

    public function getItem($id)
    {
        return ItemSubKriteria::where('id_sub_kriteria', $id)->get();
    }

   
    public function getDokumenDetail($id)
    {
        try {
            $dokumen = DB::connection('mysql_agenda_online')
            ->table('dokumens')
            ->select(
                'id as dokumen_id',
                'uraian_spp as uraian',
                'nilai_rupiah',
                'dibayar_kepada as penerima',
                'jenis_pembayaran as pembayaran'
            )
            ->where('id', $id)
            ->first();

            return response()->json([
                'success' => true,
                'data' => $dokumen
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    } 

    public function dashboard()
    {
        $total_pengeluaran = bankKeluar::select(
            DB::raw("SUM(kredit) as total")
        )
        ->groupBy(DB::raw("MONTH(tanggal)"))
        ->pluck('total');

        $bulan = bankKeluar::select(
            DB::raw("MONTHNAME(tanggal) as bulan")
        )
        ->groupBy(DB::raw("MONTHNAME(tanggal)"))
        ->pluck('bulan');

        $tahun = bankKeluar::select(
            DB::raw("YEAR(tanggal) as tahun")
        )
        ->groupBy(DB::raw("YEAR(tanggal)"))
        ->pluck('tahun');

        return view('cash_bank.dashboard', compact('total_pengeluaran', 'bulan','tahun'));
    }

public function report(Request $request) 
{
/* ================= AMBIL SEMUA REQUEST FILTER ================= */
    $tahun = $request->tahun;
    $bulan = $request->bulan;
    $tanggalDipilih = $request->tanggal;
    $bankTujuanId = $request->bank_tujuan;
    $sumberDanaIds = $request->sumber_dana;
    $kategoriIds = $request->kategori;
    $rekapanVA = $request->rekapanVA;
    $idJenisPembayaran = $request->id_jenis_pembayaran;

    /* ================= HITUNG JUMLAH FILTER AKTIF ================= */
    $activeFilters = [];
    
    // Filter waktu (tahun, bulan, tanggal) dihitung terpisah
    $timeFilters = [];
    if ($tahun) $timeFilters[] = 'tahun';
    if ($bulan) $timeFilters[] = 'bulan';
    if ($tanggalDipilih && count($tanggalDipilih) > 0) $timeFilters[] = 'tanggal';
    
    // Filter data (yang mempengaruhi tampilan kolom)
    if ($bankTujuanId) $activeFilters[] = 'bank_tujuan';
    if ($sumberDanaIds && count($sumberDanaIds) > 0) $activeFilters[] = 'sumber_dana';
    if ($kategoriIds && count($kategoriIds) > 0) $activeFilters[] = 'kategori';
    if ($idJenisPembayaran) $activeFilters[] = 'jenis_pembayaran';
    if ($rekapanVA) $activeFilters[] = 'rekapan';

    $countActiveFilters = count($activeFilters);
    $countTimeFilters = count($timeFilters);

    /* ================= FILTER TANGGAL (CLOSURE) ================= */
    $filterTanggal = function ($q) use ($tahun, $bulan, $tanggalDipilih) {
        if (!empty($tanggalDipilih) && is_array($tanggalDipilih)) {
            $q->whereIn(DB::raw('DATE(tanggal)'), $tanggalDipilih);
        } elseif ($tahun && $bulan) {
            $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        } elseif ($tahun) {
            $q->whereYear('tanggal', $tahun);
        }
    };

    /* ================= APPLY FILTER PROGRESIF ================= */
    $applyFilter = function ($q, $table = null) use (
        $filterTanggal,
        $bankTujuanId, 
        $sumberDanaIds, 
        $kategoriIds, 
        $idJenisPembayaran
    ) {
        $prefix = $table ? $table.'.' : '';
        
        // 1. Filter Tanggal (Base Filter - selalu aktif)
        $filterTanggal($q);

        // 2. Filter Bank Tujuan
        if ($bankTujuanId) {
            $q->where($prefix.'id_bank_tujuan', $bankTujuanId);
        }

        // 3. Filter Sumber Dana
        if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
            $q->whereIn($prefix.'id_sumber_dana', $sumberDanaIds);
        }

        // 4. Filter Kategori
        if ($kategoriIds && is_array($kategoriIds) && count($kategoriIds) > 0) {
            $q->whereIn($prefix.'id_kategori_kriteria', $kategoriIds);
        }

        // 5. Filter Jenis Pembayaran
        if ($idJenisPembayaran) {
            $q->where($prefix.'id_jenis_pembayaran', $idJenisPembayaran);
        }
    };

    /* ================= DROPDOWN LISTS (PROGRESSIVE - SALING TERHUBUNG) ================= */
    
    // Tahun List
    $tahunList = collect()
        ->merge(DB::table('bank_masuk')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
        ->merge(DB::table('bank_keluars')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
        ->unique()->sortDesc()->values();

    // Bulan List (tergantung tahun)
    $bulanList = collect()
        ->merge(
            DB::table('bank_masuk')
                ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
                ->selectRaw('MONTH(tanggal) as bulan')
                ->pluck('bulan')
        )
        ->merge(
            DB::table('bank_keluars')
                ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
                ->selectRaw('MONTH(tanggal) as bulan')
                ->pluck('bulan')
        )
        ->unique()->sort()->values();

    // Tanggal List (tergantung tahun & bulan)
    $tanggalList = collect()
        ->merge(
            DB::table('bank_masuk')
                ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
                ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
                ->selectRaw('DATE(tanggal) as tanggal')
                ->pluck('tanggal')
        )
        ->merge(
            DB::table('bank_keluars')
                ->when($tahun, fn($q) => $q->whereYear('tanggal', $tahun))
                ->when($bulan, fn($q) => $q->whereMonth('tanggal', $bulan))
                ->selectRaw('DATE(tanggal) as tanggal')
                ->pluck('tanggal')
        )
        ->unique()->sort()->values();

    // Bank Tujuan List (terhubung dengan semua filter lain)
    $bankTujuanList = DB::table('bank_tujuan')
        ->where(function($query) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
            $query->whereExists(function($sub) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
                $sub->select(DB::raw(1))
                    ->from('bank_keluars')
                    ->whereColumn('bank_keluars.id_bank_tujuan', 'bank_tujuan.id_bank_tujuan')
                    ->where(function($q) use ($filterTanggal, $sumberDanaIds, $kategoriIds, $idJenisPembayaran) {
                        $filterTanggal($q);
                        if ($sumberDanaIds && count($sumberDanaIds) > 0) {
                            $q->whereIn('id_sumber_dana', $sumberDanaIds);
                        }
                        if ($kategoriIds && count($kategoriIds) > 0) {
                            $q->whereIn('id_kategori_kriteria', $kategoriIds);
                        }
                        if ($idJenisPembayaran) {
                            $q->where('id_jenis_pembayaran', $idJenisPembayaran);
                        }
                    });
            })
            ->orWhereExists(function($sub) use ($filterTanggal, $sumberDanaIds) {
                $sub->select(DB::raw(1))
                    ->from('bank_masuk')
                    ->whereColumn('bank_masuk.id_bank_tujuan', 'bank_tujuan.id_bank_tujuan')
                    ->where(function($q) use ($filterTanggal, $sumberDanaIds) {
                        $filterTanggal($q);
                        if ($sumberDanaIds && count($sumberDanaIds) > 0) {
                            $q->whereIn('id_sumber_dana', $sumberDanaIds);
                        }
                    });
            });
        })
        ->orderBy('nama_tujuan')
        ->get();

    // Sumber Dana List (terhubung dengan semua filter lain)
    $sumberDanaList = DB::table('sumber_dana')
        ->where(function($query) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
            $query->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
                $sub->select(DB::raw(1))
                    ->from('bank_keluars')
                    ->whereColumn('bank_keluars.id_sumber_dana', 'sumber_dana.id_sumber_dana')
                    ->where(function($q) use ($filterTanggal, $bankTujuanId, $kategoriIds, $idJenisPembayaran) {
                        $filterTanggal($q);
                        if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
                        if ($kategoriIds && count($kategoriIds) > 0) {
                            $q->whereIn('id_kategori_kriteria', $kategoriIds);
                        }
                        if ($idJenisPembayaran) $q->where('id_jenis_pembayaran', $idJenisPembayaran);
                    });
            })
            ->orWhereExists(function($sub) use ($filterTanggal, $bankTujuanId) {
                $sub->select(DB::raw(1))
                    ->from('bank_masuk')
                    ->whereColumn('bank_masuk.id_sumber_dana', 'sumber_dana.id_sumber_dana')
                    ->where(function($q) use ($filterTanggal, $bankTujuanId) {
                        $filterTanggal($q);
                        if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
                    });
            });
        })
        ->orderBy('nama_sumber_dana')
        ->get();

    // Kategori List (terhubung dengan filter lain)
    $kategoriList = DB::table('kategori_kriteria')
        ->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $idJenisPembayaran) {
            $sub->select(DB::raw(1))
                ->from('bank_keluars')
                ->whereColumn('bank_keluars.id_kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria')
                ->where(function($q) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $idJenisPembayaran) {
                    $filterTanggal($q);
                    if ($bankTujuanId) $q->where('id_bank_tujuan', $bankTujuanId);
                    if ($sumberDanaIds && count($sumberDanaIds) > 0) {
                        $q->whereIn('id_sumber_dana', $sumberDanaIds);
                    }
                    if ($idJenisPembayaran) $q->where('id_jenis_pembayaran', $idJenisPembayaran);
                });
        })
        ->orderBy('nama_kriteria')
        ->get();

    // Jenis Pembayaran List (terhubung dengan filter lain)
    $jenisPembayaranList = DB::table('jenis_pembayarans')
        ->whereExists(function($sub) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
            $sub->select(DB::raw(1))
                ->from('bank_keluars')
                ->whereColumn('bank_keluars.id_jenis_pembayaran', 'jenis_pembayarans.id_jenis_pembayaran')
                ->where(function($q) use ($filterTanggal, $bankTujuanId, $sumberDanaIds, $kategoriIds) {
                    $filterTanggal($q);
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
    

    /* ================= LOGIKA TAMPILAN DATA ================= */
    
    // Tentukan tampilan berdasarkan filter aktif
    $showDebet = false;
    $showSaldoAkhir = false;
    $showSAP = false;
    
    // Jika hanya sumber dana yang dipilih (atau tidak ada filter sama sekali)
    if ($countActiveFilters == 0 || (in_array('sumber_dana', $activeFilters) && $countActiveFilters == 1)) {
        $showDebet = true;
        $showSaldoAkhir = true;
        $showSAP = true;
    }
    // Jika hanya bank tujuan yang dipilih
    elseif (in_array('bank_tujuan', $activeFilters) && $countActiveFilters == 1) {
        $showDebet = true;
        $showSaldoAkhir = true;
        $showSAP = true;
    }
    // Jika kombinasi 2 atau lebih filter (lebih spesifik)
    elseif ($countActiveFilters >= 2) {
        $showDebet = false;
        $showSaldoAkhir = false;
        $showSAP = false;
    }

    /* ================= QUERY DATA UTAMA ================= */
    
    if ($showDebet) {
        // Bank Masuk (Debet)
        $bankMasuk = DB::table('bank_masuk')
            ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_masuk.id_sumber_dana')
            ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_masuk.id_bank_tujuan')
            ->select(
                'bank_masuk.agenda_tahun',
                'bank_masuk.id_sumber_dana',
                'sumber_dana.nama_sumber_dana',
                'bank_masuk.id_bank_tujuan',
                'bank_tujuan.nama_tujuan',
                'bank_masuk.uraian',
                'bank_masuk.penerima',
                'bank_masuk.tanggal',
                'bank_masuk.debet',
                DB::raw('0 as kredit'),
                'bank_masuk.no_sap',
                DB::raw('NULL as nama_kriteria'),
                DB::raw('NULL as nama_sub_kriteria'),
                DB::raw('NULL as nama_item_sub_kriteria'),
                DB::raw('NULL as id_jenis_pembayaran'),
                DB::raw('NULL as nama_jenis_pembayaran'),
                DB::raw("'MASUK' as jenis"),
                DB::raw('bank_masuk.id_bank_masuk as urut_id')
            )
            ->where(function($q) use ($applyFilter) {
                $applyFilter($q, 'bank_masuk');
            });
        
        }

    // Bank Keluar (Kredit) - Selalu tampil
    $bankKeluar = DB::table('bank_keluars')
        ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_keluars.id_sumber_dana')
        ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_keluars.id_bank_tujuan')
        ->leftJoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
        ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
        ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
        ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_keluars.id_jenis_pembayaran')
        ->select(
            'bank_keluars.agenda_tahun',
            'bank_keluars.id_sumber_dana',
            'sumber_dana.nama_sumber_dana',
            'bank_keluars.id_bank_tujuan',
            'bank_tujuan.nama_tujuan',
            'bank_keluars.uraian',
            'bank_keluars.penerima',
            'bank_keluars.tanggal',
            DB::raw('0 as debet'),
            'bank_keluars.kredit',
            'bank_keluars.no_sap',
            'kategori_kriteria.nama_kriteria',
            'sub_kriteria.nama_sub_kriteria',
            'item_sub_kriteria.nama_item_sub_kriteria',
            'bank_keluars.id_jenis_pembayaran',
            'jenis_pembayarans.nama_jenis_pembayaran',
            DB::raw("'KELUAR' as jenis"),
            DB::raw('bank_keluars.id_bank_keluar as urut_id')
        )
        ->where(function($q) use ($applyFilter) {
            $applyFilter($q, 'bank_keluars');
        });

    // Union
    if ($showDebet) {
        $data = $bankMasuk
            ->unionAll($bankKeluar)
            ->orderBy('tanggal')
            ->orderBy('urut_id')
            ->get();
    } else {
        $data = $bankKeluar
            ->orderBy('tanggal')
            ->orderBy('urut_id')
            ->get();
    }

    // Hitung Saldo Berjalan (hanya jika showSaldoAkhir)
    if ($showSaldoAkhir) {
        $saldo = 0;
        foreach ($data as $d) {
            $saldo += ($d->debet ?? 0) - ($d->kredit ?? 0);
            $d->saldo_akhir = $saldo;
        }
    } else {
        foreach ($data as $d) {
            $d->saldo_akhir = null;
        }
    }

    /* ================= REKAPAN (PROGRESSIVE) ================= */
    $rekapVA = [];

    // Rekap Saldo Bank
    if ($rekapanVA === 'va') {
        $rekapBanks = DB::table('bank_tujuan')
            ->when($bankTujuanId, fn($q) => $q->where('id_bank_tujuan', $bankTujuanId))
            ->get();

        foreach ($rekapBanks as $bank) {
            $debet = 0;
            $kredit = 0;

            if ($showDebet) {
                $debet = DB::table('bank_masuk')
                    ->where('id_bank_tujuan', $bank->id_bank_tujuan)
                    ->where(function($q) use ($applyFilter) {
                        $applyFilter($q, 'bank_masuk');
                    })
                    ->sum('debet');
            }

            $kredit = DB::table('bank_keluars')
                ->where('id_bank_tujuan', $bank->id_bank_tujuan)
                ->where(function($q) use ($applyFilter) {
                    $applyFilter($q, 'bank_keluars');
                })
                ->sum('kredit');
            $saldo = 0;
            foreach ($data as $d) {
                $saldo += ($d->debet ?? 0) - ($d->kredit ?? 0);
                $d->saldo_akhir = $saldo;

            if ($debet > 0 || $kredit > 0) {
                $rekapVA[] = [
                    'bank' => $bank->nama_tujuan,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'saldo_va' => $saldo,
                    'saldo_sap' => $bankKeluar->no_sap ?? '-',
                    'selisih' => $saldo,
                    'keterangan' => '-'
                ];
            }
        }
    }

    // Rekap Saldo VA
    if ($rekapanVA === 'bank') {
        $rekapSDs = DB::table('sumber_dana')
            ->when($sumberDanaIds, fn($q) => $q->whereIn('id_sumber_dana', $sumberDanaIds))
            ->get();

        foreach ($rekapSDs as $sd) {
            $debet = 0;
            $kredit = 0;

            if ($showDebet) {
                $debet = DB::table('bank_masuk')
                    ->where('id_sumber_dana', $sd->id_sumber_dana)
                    ->where(function($q) use ($applyFilter) {
                        $applyFilter($q, 'bank_masuk');
                    })
                    ->sum('debet');
            }

            $kredit = DB::table('bank_keluars')
                ->where('id_sumber_dana', $sd->id_sumber_dana)
                ->where(function($q) use ($applyFilter) {
                    $applyFilter($q, 'bank_keluars');
                })
                ->sum('kredit');
            $saldo = 0;
            foreach ($data as $d) {
                $saldo += ($d->debet ?? 0) - ($d->kredit ?? 0);
                $d->saldo_akhir = $saldo;
            if ($debet > 0 || $kredit > 0) {
                $rekapVA[] = [
                    'bank' => $sd->nama_sumber_dana,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'saldo_va' => $saldo,
                    // 'saldo_sap' => $bankKeluar->no_sap,
                    // 'selisih' => 0,
                    'keterangan' => '-'
                ];
            }
        }
    }

    // Rekap Kategori Full (dengan filter progresif)
    if ($rekapanVA === 'kategori-full') {
        $dataKategori = DB::table('bank_keluars')
            ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
            ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
            ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
            ->where(function($q) use ($applyFilter) {
                $applyFilter($q, 'bank_keluars');
            })
            ->select(
                'kategori_kriteria.nama_kriteria as kategori',
                'sub_kriteria.nama_sub_kriteria as sub',
                'item_sub_kriteria.nama_item_sub_kriteria as item',
                DB::raw('SUM(bank_keluars.kredit) as kredit')
            )
            ->groupBy('kategori', 'sub', 'item')
            ->orderBy('kategori')
            ->orderBy('sub')
            ->orderBy('item')
            ->get();

        foreach ($dataKategori as $row) {
            $rekapVA[$row->kategori][$row->sub][] = [
                'item' => $row->item,
                'kredit' => (float)$row->kredit
            ];
        }
    }

    // Rekap Total Kategori
    $rekapKategori = DB::table('bank_keluars')
        ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
        ->where(function($q) use ($applyFilter) {
            $applyFilter($q, 'bank_keluars');
        })
        ->select(
            'kategori_kriteria.id_kategori_kriteria',
            'kategori_kriteria.nama_kriteria',
            DB::raw('SUM(bank_keluars.kredit) as total_kredit')
        )
        ->groupBy('kategori_kriteria.id_kategori_kriteria', 'kategori_kriteria.nama_kriteria')
        ->get();

    foreach ($kategoriList as $kat) {
        $total = $rekapKategori->where('id_kategori_kriteria', $kat->id_kategori_kriteria)->first();
        $kat->total_kredit = $total ? $total->total_kredit : 0;
    }

    // Rekap Jenis Pembayaran
    $rekapJenisPembayaran = DB::table('bank_keluars')
        ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_keluars.id_jenis_pembayaran')
        ->where(function($q) use ($applyFilter) {
            $applyFilter($q, 'bank_keluars');
        })
        ->select(
            'bank_keluars.id_jenis_pembayaran',
            'jenis_pembayarans.nama_jenis_pembayaran',
            DB::raw('SUM(bank_keluars.kredit) as total')
        )
        ->groupBy('bank_keluars.id_jenis_pembayaran', 'jenis_pembayarans.nama_jenis_pembayaran')
        ->get();

    // Data Agenda
    $agendaData = collect();
    if ($idJenisPembayaran) {
        $agendaData = DB::table('bank_keluars')
            ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_keluars.id_sumber_dana')
            ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_keluars.id_bank_tujuan')
            ->leftJoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
            ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
            ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
            ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_keluars.id_jenis_pembayaran')
            ->where(function($q) use ($applyFilter) {
                $applyFilter($q, 'bank_keluars');
            })
            ->select(
                'bank_keluars.tanggal',
                'bank_keluars.agenda_tahun',
                'bank_keluars.uraian',
                'bank_keluars.penerima',
                'sumber_dana.nama_sumber_dana',
                'bank_tujuan.nama_tujuan',
                'kategori_kriteria.nama_kriteria',
                'sub_kriteria.nama_sub_kriteria',
                'item_sub_kriteria.nama_item_sub_kriteria',
                'jenis_pembayarans.nama_jenis_pembayaran as jenis_pembayaran',
                'bank_keluars.kredit'
            )
            ->orderBy('bank_keluars.tanggal')
            ->get();
            }

    /* ================= RETURN VIEW ================= */
    return view('cash_bank.reportKeluar', compact(
        'data',
        'tahunList',
        'bulanList',
        'tanggalList',
        'bankTujuanList',
        'sumberDanaList',
        'kategoriList',
        'jenisPembayaranList',
        'rekapJenisPembayaran',
        'rekapVA',
        'agendaData',
        'showDebet',
        'showSaldoAkhir',
        'showSAP',
        'countActiveFilters',
        'countTimeFilters',
        'activeFilters',
        'timeFilters'
    ));
}

    public function importExcel(Request $request)
    {
        $request->validate([
            'fileExcel' => 'required|mimes:xlsx,xls'
        ]);
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $file = $request->file('fileExcel')->store('public/import');

        Excel::import(new importKeluar, $file);

        return redirect()
            ->route('bank-keluar.index')
            ->with('success', 'Data berhasil diimport');
    };
    
    public function edit(string $id)
    {
        $keluar = bankKeluar::findOrFail($id);
        return view('cash_bank.modal.editKeluar', compact('keluar'));
    }
    public function update(Request $request, string $id)
    {

        $keluar = bankKeluar::findOrFail($id);
        $keluar->update($request->all());

        return redirect()->route('bank-keluar.index')->with('success', 'Data berhasil diperbarui');
    }

      public function destroy(string $id)
    {
        $data = bankKeluar::findOrFail($id);
        $data->delete();

        return redirect()->route('bank-keluar.index')->with('success', 'Data berhasil dihapus');
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;

        BankKeluar::whereIn('id_bank_keluar', $ids)->delete();

        return response()->json([
            'success' => 'Data Bank Keluar Berhasil Dihapus!'
        ]);
    }
}
