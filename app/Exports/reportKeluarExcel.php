<?php

namespace App\Exports;

use App\Models\BankTujuan;
use App\Models\SumberDana;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Fromview;

class reportKeluarExcel implements Fromview
{
    /**
    * @return \Illuminate\Support\view
    */

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function view() :View
    {

    /* ================= AMBIL SEMUA REQUEST FILTER ================= */
    $tahun = $this->request->tahun;
    $bulan = $this->request->bulan;
    $tanggalDipilih = $this->request->tanggal;
    $bankTujuanId = $this->request->bank_tujuan;
    $sumberDanaIds = $this->request->sumber_dana;
    $kategoriIds = $this->request->kategori;
    $idJenisPembayaran = $this->request->id_jenis_pembayaran;
    $rekapanVA = $this->request->rekapanVA;

    /* ================= HITUNG JUMLAH FILTER AKTIF ================= */
    $activeFilters = [];
    $timeFilters = [];
    
    if ($tahun) $timeFilters[] = 'tahun';
    if ($bulan) $timeFilters[] = 'bulan';
    if ($tanggalDipilih && count($tanggalDipilih) > 0) $timeFilters[] = 'tanggal';
    
    if ($bankTujuanId) $activeFilters[] = 'bank_tujuan';
    if ($sumberDanaIds && count($sumberDanaIds) > 0) $activeFilters[] = 'sumber_dana';
    if ($kategoriIds && count($kategoriIds) > 0) $activeFilters[] = 'kategori';
    if ($idJenisPembayaran) $activeFilters[] = 'jenis_pembayaran';
    if ($rekapanVA) $activeFilters[] = 'rekapan';
    
    $countActiveFilters = count($activeFilters);

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
        
        $filterTanggal($q);
        
        if ($bankTujuanId) {
            $q->where($prefix.'id_bank_tujuan', $bankTujuanId);
        }
        
        if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
            $q->whereIn($prefix.'id_sumber_dana', $sumberDanaIds);
        }
        
        if ($kategoriIds && is_array($kategoriIds) && count($kategoriIds) > 0) {
            $q->whereIn($prefix.'id_kategori_kriteria', $kategoriIds);
        }
        
        if ($idJenisPembayaran) {
            $q->where($prefix.'id_jenis_pembayaran', $idJenisPembayaran);
        }
    };

    /* ================= FILTER KHUSUS UNTUK SALDO AWAL ================= */
    // Filter untuk hitung saldo awal (hanya filter waktu, bank, dan sumber dana)
    $applyFilterSaldoAwal = function ($q, $table = null) use (
        $filterTanggal,
        $bankTujuanId,
        $sumberDanaIds
    ) {
        $prefix = $table ? $table.'.' : '';
        
        $filterTanggal($q);
        
        if ($bankTujuanId) {
            $q->where($prefix.'id_bank_tujuan', $bankTujuanId);
        }
        
        if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
            $q->whereIn($prefix.'id_sumber_dana', $sumberDanaIds);
        }
    };

    /* ================= DROPDOWN LISTS ================= */
    $tahunList = collect()
        ->merge(DB::table('bank_masuk')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
        ->merge(DB::table('bank_keluars')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
        ->unique()->sortDesc()->values();

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
    $showDebet = false;
    $showSaldoAkhir = false;
    $showSAP = false;

    // LOGIKA BARU: 
    // 1 filter atau tanpa filter = tampil DEBET + KREDIT + SALDO AKHIR
    // 2+ filter = tampil KREDIT saja + TOTAL KREDIT
    
    if ($countActiveFilters == 0) {
        // Tidak ada filter (tampil semua)
        $showDebet = true;
        $showSaldoAkhir = true;
        $showSAP = true;
    } elseif ($countActiveFilters == 1) {
        // 1 filter saja (bank_tujuan, sumber_dana, atau rekapan)
        $showDebet = true;
        $showSaldoAkhir = true;
        $showSAP = true;
    } else {
        // 2 atau lebih filter = hanya kredit
        $showDebet = false;
        $showSaldoAkhir = false;
        $showSAP = false;
    }

    /* ================= QUERY DATA UTAMA ================= */
    if ($showDebet) {
        // Tampilkan Bank Masuk (Debet) + Bank Keluar (Kredit)
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
            ->where(function($q) use ($applyFilterSaldoAwal) {
                // Gunakan filter saldo awal (tanpa kategori/jenis pembayaran)
                $applyFilterSaldoAwal($q, 'bank_masuk');
            });

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
            ->where(function($q) use ($applyFilterSaldoAwal) {
                // Gunakan filter saldo awal (tanpa kategori/jenis pembayaran)
                $applyFilterSaldoAwal($q, 'bank_keluars');
            });

        $data = $bankMasuk
            ->unionAll($bankKeluar)
            ->orderBy('tanggal')
            ->orderBy('urut_id')
            ->get();
    } else {
        // Hanya tampilkan Bank Keluar (Kredit) dengan filter lengkap
        $data = DB::table('bank_keluars')
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
                // Gunakan filter lengkap (dengan kategori/jenis pembayaran)
                $applyFilter($q, 'bank_keluars');
            })
            ->orderBy('tanggal')
            ->orderBy('urut_id')
            ->get();
    }

    /* ================= HITUNG SALDO BERJALAN / TOTAL KREDIT ================= */
    if ($showSaldoAkhir) {
        // Mode: Tampil Debet + Kredit + Saldo Akhir
        // Karena $data sudah berisi semua bank_masuk dan bank_keluar yang difilter
        // Kita bisa langsung hitung saldo berjalan
        $saldo = 0;
        foreach ($data as $d) {
            $saldo += ($d->debet ?? 0) - ($d->kredit ?? 0);
            $d->saldo_akhir = $saldo;
        }
    } else {
        // Mode: Hanya Kredit + Total Kredit
        foreach ($data as $d) {
            $d->saldo_akhir = null;
        }
    }

    // Hitung Total Kredit (untuk mode 2+ filter)
    $totalKredit = $data->sum('kredit');

    /* ================= REKAPAN ================= */
    $rekapVA = [];
    
    if ($rekapanVA === 'bank' && $tahun) {
        foreach (BankTujuan::all() as $bank) {
            $debetTotal = DB::table('bank_masuk')
                ->whereYear('tanggal', $tahun)
                ->where('id_bank_tujuan', $bank->id_bank_tujuan)
                ->when($sumberDanaIds && count($sumberDanaIds) > 0, function($q) use ($sumberDanaIds) {
                    $q->whereIn('id_sumber_dana', $sumberDanaIds);
                })
                ->sum('debet');
            
            $kreditTotal = DB::table('bank_keluars')
                ->whereYear('tanggal', $tahun)
                ->where('id_bank_tujuan', $bank->id_bank_tujuan)
                ->when($sumberDanaIds && count($sumberDanaIds) > 0, function($q) use ($sumberDanaIds) {
                    $q->whereIn('id_sumber_dana', $sumberDanaIds);
                })
                ->sum('kredit');
            
            $saldo = $debetTotal - $kreditTotal;
            
            if ($saldo != 0 || $debetTotal != 0 || $kreditTotal != 0) {
                $rekapVA[] = [
                    'bank' => $bank->nama_tujuan,
                    'saldo_va' => $saldo,
                    'saldo_sap' => 0,
                    'selisih' => $saldo,
                    'keterangan' => "Saldo akhir tahun {$tahun}"
                ];
            }
        }
    }
    
    if ($rekapanVA === 'va' && $tahun) {
        foreach (SumberDana::all() as $sd) {
            $debetTotal = DB::table('bank_masuk')
                ->whereYear('tanggal', $tahun)
                ->where('id_sumber_dana', $sd->id_sumber_dana)
                ->when($bankTujuanId, function($q) use ($bankTujuanId) {
                    $q->where('id_bank_tujuan', $bankTujuanId);
                })
                ->sum('debet');
            
            $kreditTotal = DB::table('bank_keluars')
                ->whereYear('tanggal', $tahun)
                ->where('id_sumber_dana', $sd->id_sumber_dana)
                ->when($bankTujuanId, function($q) use ($bankTujuanId) {
                    $q->where('id_bank_tujuan', $bankTujuanId);
                })
                ->sum('kredit');
            
            $saldo = $debetTotal - $kreditTotal;
            
            if ($saldo != 0 || $debetTotal != 0 || $kreditTotal != 0) {
                $rekapVA[] = [
                    'bank' => $sd->nama_sumber_dana,
                    'saldo_va' => $saldo,
                    'saldo_sap' => 0,
                    'selisih' => $saldo,
                    'keterangan' => "Saldo akhir tahun {$tahun}"
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

    return view('cash_bank.exportExcel.excelReportKeluar', compact(
        'data',
        'tahunList',
        'bulanList',
        'tanggalList',
        'bankTujuanList',
        'sumberDanaList',
        'kategoriList',
        'jenisPembayaranList',
        'showDebet',
        'showSaldoAkhir',
        'showSAP',
        'rekapVA',
        'totalKredit',
        'tahun',
        'bulan',
        'tanggalDipilih',
        'bankTujuanId',
        'sumberDanaIds',
        'kategoriIds',
        'idJenisPembayaran',
        'rekapanVA',
        'countActiveFilters'
    ));
    }
}
