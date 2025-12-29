    // public function report(Request $request)
    // {
    //     $search = $request->keyword;
    //     $tahun = $request->tahun;
    //     $bankTujuanId = $request->bank_tujuan;
    //     $sumberDanaIds = $request->sumber_dana;
    //     $bulan         = $request->bulan;
    //     $tglAwal       = $request->tanggal_awal;
    //     $tglAkhir      = $request->tanggal_akhir;
    //     $bankTujuanId  = $request->bank_tujuan;
    //     $sumberDanaIds = $request->sumber_dana;
    //     $jenisPembayaranIds   = $request->jenis_pembayaran;

    // /* ================= FILTER TANGGAL ================= */
    // $filterTanggal = function ($q) use ($tglAwal, $tglAkhir, $tahun, $bulan) {
    //     if ($tglAwal && $tglAkhir) {
    //         $q->whereBetween('tanggal', [$tglAwal, $tglAkhir]);
    //     } elseif ($tahun && $bulan) {
    //         $q->whereYear('tanggal', $tahun)
    //           ->whereMonth('tanggal', $bulan);
    //     } elseif ($tahun) {
    //         $q->whereYear('tanggal', $tahun);
    //     }
    // };

    //     $tahunList = BankMasuk::select(DB::raw('YEAR(tanggal) as tahun'))
    //         ->groupBy(DB::raw('YEAR(tanggal)'))
    //         ->orderByDesc('tahun')
    //         ->pluck('tahun');

    //     $query = BankMasuk::with(['bankTujuan','sumberDana','kategori','jenisPembayaran'])
    //         ->orderBy('tanggal','asc');

    //     if ($tahun) {
    //         $query->whereYear('tanggal',$tahun);
    //     }

    //     if ($bankTujuanId) {
    //         $query->where('id_bank_tujuan',$bankTujuanId);
    //     }

    //     if ($sumberDanaIds && is_array($sumberDanaIds)) {
    //         $query->whereIn('id_sumber_dana',$sumberDanaIds);
    //     }
    //     if ($jenisPembayaranIds && is_array($jenisPembayaranIds)) {
    //         $query->whereIn('id_jenis_pembayaran',$jenisPembayaranIds);
    //     }

    //     if ($search) {
    //         $query->where(function($q) use ($search) {
    //             $q->where('agenda_tahun','like',"%{$search}%")
    //               ->orWhere('uraian','like',"%{$search}%")
    //               ->orWhere('penerima','like',"%{$search}%")
    //               ->orWhere('jenisPembayaran','like',"%{$search}%")
    //               ->orWhereHas('bankTujuan', fn($x)=>
    //                     $x->where('nama_tujuan','like',"%{$search}%"));
    //         });
    //     }

    //     $data = $query->get();

    //     // saldo berjalan
    //     $saldoAkhir = 0;
    //     foreach ($data as $row) {
    //         $saldoAkhir += ($row->debet ?? 0) - ($row->kredit ?? 0);
    //         $row->saldo_akhir = $saldoAkhir;
    //     }

    //     $rekapJenisPembayaran = DB::table('bank_masuk')
    //     ->join('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_masuk.id_jenis_pembayaran')
    //     ->select(
    //         'jenis_pembayarans.id_jenis_pembayaran',
    //         'jenis_pembayarans.nama_jenis_pembayaran'
    //     )
    //     ->whereNotNull('bank_masuk.id_jenis_pembayaran')
    //     ->distinct()
    //     ->orderBy('jenis_pembayarans.nama_jenis_pembayaran')
    //     ->get();

    // /* ================= KATEGORI ================= */
    //     $kategoriList = DB::table('bank_masuk')
    //     ->join('kategori_kriteria','kategori_kriteria.id_kategori_kriteria','=','bank_masuk.id_kategori_kriteria')
    //     ->select(
    //         'kategori_kriteria.id_kategori_kriteria',
    //         'kategori_kriteria.nama_kriteria',
    //         DB::raw('SUM(bank_masuk.debet) as total_kredit')
    //     )
    //     ->where($filterTanggal)
    //     ->where('tipe','Masuk')
    //     ->when($sumberDanaIds, fn($q)=>$q->whereIn('bank_masuk.id_sumber_dana',$sumberDanaIds))
    //     ->groupBy('kategori_kriteria.id_kategori_kriteria','kategori_kriteria.nama_kriteria')
    //     ->get();


    //     return view('cash_bank.reportMasuk', [
    //         'data' => $data,
    //         'tahunList' => $tahunList,
    //         'bankTujuanList' => BankTujuan::all(),
    //         'sumberDanaList' => SumberDana::all(),
    //         'rekapJenisPembayaran' => JenisPembayaran::all(),
    //         'kategoriList' => KategoriKriteria::where('tipe','Masuk')->get(),
    //         'selectedTahun' => $tahun,
    //         'selectedBankTujuan' => $bankTujuanId,
    //         'selectedSumberDana' => $sumberDanaIds,
    //         'selectedJenisPembayaran' => $jenisPembayaranIds,
    //     ]);
    // }