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

// class BankMasukController extends Controller
// {
//     public function index(Request $request)
//     {
//         $search = $request->keyword;
//         $data = GabunganMasukKeluar::where('jenis', 'Masuk')
//         ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'gabungan_masuk_keluars.id_sumber_dana')
//         ->leftJoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
//         ->when($search, function ($query, $search) {

//             return $query->where(function ($q) use ($search) {
//                 $q->where('gabungan_masuk_keluars.tanggal', 'like', "%{$search}%")
//                 ->orWhere('gabungan_masuk_keluars.agenda_tahun', 'like', "%{$search}%")
//                 ->orWhere('gabungan_masuk_keluars.penerima', 'like', "%{$search}%")
//                 ->orWhere('sumber_dana.nama_sumber_dana', 'like', "%{$search}%")
//                 ->orWhere('kategori_kriteria.nama_kriteria', 'like', "%{$search}%")
//                 ->orWhere('gabungan_masuk_keluars.debet', 'like', "%{$search}%")
//                 ->orWhere('gabungan_masuk_keluars.uraian', 'like', "%{$search}%");
//             });

//         })
//         ->select('gabungan_masuk_keluars.*')
//         ->latest()
//         ->get();
//         return view('cash_bank.bankMasuk', [
//             'data' => $data,
//             'sumberDana'        => SumberDana::all(),
//             'bankTujuan'        => BankTujuan::all(),
//             'kategoriKriteria' => KategoriKriteria::where('tipe', 'Masuk')->get(),
//         ]);
//     }

//     public function create()
//     {
//         return view('modal.tambahMasuk');
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'agenda_tahun' => 'nullable|string',
//             'id_kategori_kriteria' => 'nullable|string',
//             'uraian' => 'nullable|string',
//             'tanggal' => 'required|date',
//             'debet' => 'required|numeric',
//             'pembayaran' => 'nullable|string',
//             'keterangan' => 'nullable|string',
//         ]);

//         GabunganMasukKeluar::create([
//             'agenda_tahun' => $request->agenda_tahun ?? null,
//             'id_sumber_dana' => $request->id_sumber_dana ?? null,
//             'id_bank_tujuan' => $request->id_bank_tujuan ?? null,
//             'id_kategori_kriteria' => $request->id_kategori_kriteria ?? null,
//             'uraian' => $request->uraian ?? null,
//             'penerima' => $request->penerima ?? null,
//             'jenis_pembayaran' => $request->pembayaran ?? null,
//             'tanggal' => $request->tanggal,
//             'debet' => $validated['debet'],
//             'kredit' => 0,
//             'keterangan' => $request->keterangan ,
//             'jenis' => 'Masuk'
//         ]);


//         return redirect()->back()->with('success', 'Data berhasil disimpan!');
//     }


//     public function dashboard()
//     {
//         $total_pemasukkan = GabunganMasukKeluar::select(
//             DB::raw("SUM(debet) as total")
//         )
//         ->groupBy(DB::raw("MONTH(created_at)"))
//         ->pluck('total');

//         $bulan = bankKeluar::select(
//             DB::raw("MONTHNAME(created_at) as bulan")
//         )
//         ->groupBy(DB::raw("MONTHNAME(created_at)"))
//         ->pluck('bulan');

//         return view('cash_bank.dashboard', compact('total_pemasukkan', 'bulan'));
//     }

//     public function report(Request $request) {
//         $search = $request->keyword;
//         $tahun = $request->tahun;
//         $bankTujuanId = $request->bank_tujuan;
//         $sumberDanaIds = $request->sumber_dana; // Sekarang array
//         $rekapanVA = $request->rekapanVA;
//         $filterJenis = $request->jenis_pembayaran;

//         // Ambil daftar tahun untuk dropdown
//         $tahunList = GabunganMasukKeluar::select(DB::raw('YEAR(tanggal) as tahun'))
//             ->groupBy(DB::raw('YEAR(tanggal)'))
//             ->orderByDesc('tahun')
//             ->pluck('tahun');
        
        

//         // Query utama untuk data bank keluar
//         $query = GabunganMasukKeluar::with(['bankTujuan', 'sumberDana'])
//             // ->where('jenis', 'Keluar') // 🔥 INI KUNCI
//             ->orderBy('tanggal', 'asc');

//         if ($tahun) {
//             $query->whereYear('tanggal', $tahun);
//         }

//         if ($bankTujuanId) {
//             $query->where('id_bank_tujuan', $bankTujuanId);
//         }


//         // Filter Sumber Dana - handle multiple selection
//         if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//             $query->whereIn('id_sumber_dana', $sumberDanaIds);
//         }
        
//         if ($kategoriIds = request('kategori')) {
//             $query->whereIn('id_kategori_kriteria', $kategoriIds);
//         }

//         $kategoriList = KategoriKriteria::where('tipe', 'Masuk')
//             ->orderBy('nama_kriteria')
//             ->get();
        

//         if ($search) {
//             $query->where(function($q) use ($search) {
//                 $q->where('tanggal', 'like', "%{$search}%")
//                 ->orWhere('penerima', 'like', "%{$search}%")
//                 ->orWhere('uraian', 'like', "%{$search}%")
//                 ->orWhere('agenda_tahun', 'like', "%{$search}%")
//                 ->orWhereHas('bankTujuan', function($q) use ($search) {
//                     $q->where('nama_tujuan', 'like', "%{$search}%");
//                 });
//             });
//         }

//         $data = $query->get();

//         // Hitung saldo akhir
        // $saldoAkhir = 0;
        // foreach ($data as $row) {
        //     $saldoAkhir += ($row->debet ?? 0) - ($row->kredit ?? 0);
        //     $row->saldo_akhir = $saldoAkhir;
        // }

//         // Query untuk agendaData (jenis pembayaran)
//         $agendaData = GabunganMasukKeluar::select(
//                 'gabungan_masuk_keluars.id_gabungan',
//                 'gabungan_masuk_keluars.tanggal',
//                 'gabungan_masuk_keluars.nomor_agenda',
//                 'gabungan_masuk_keluars.agenda_tahun',
//                 'gabungan_masuk_keluars.uraian',
//                 'gabungan_masuk_keluars.kredit',
//                 'gabungan_masuk_keluars.penerima',
//                 'gabungan_masuk_keluars.jenis_pembayaran'
//             )
//             ->where('gabungan_masuk_keluars.jenis', 'Masuk')
//             ->where('gabungan_masuk_keluars.kredit', '>', 0);

//         if ($tahun) {
//             $agendaData->whereYear('gabungan_masuk_keluars.tanggal', $tahun);
//         }

//         // Filter Sumber Dana untuk agenda
//         if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//             $agendaData->whereIn('gabungan_masuk_keluars.id_sumber_dana', $sumberDanaIds);
//         }

//         if ($filterJenis === '_null') {
//             $agendaData->whereNull('gabungan_masuk_keluars.jenis_pembayaran');
//         } elseif (!empty($filterJenis)) {
//             $agendaData->where('gabungan_masuk_keluars.jenis_pembayaran', $filterJenis);
//         }

//         $agendaData = $agendaData->orderBy('gabungan_masuk_keluars.agenda_tahun')
//             ->orderBy('gabungan_masuk_keluars.nomor_agenda')
//             ->get();

//         // Rekap jenis pembayaran
//         $rekapJenisPembayaran = GabunganMasukKeluar::select('jenis_pembayaran')
//             ->where('jenis', 'Keluar')
//             ->where('kredit', '>', 0)
//             ->groupBy('jenis_pembayaran')
//             ->orderBy('jenis_pembayaran')
//             ->get();

//         // REKAP
//         $rekap = [];
//         if ($rekapanVA && $tahun) {
//             switch($rekapanVA) {
//                 case 'bank':
//                     foreach (BankTujuan::all() as $bank) {
//                         $bankQuery = GabunganMasukKeluar::whereYear('tanggal', $tahun)
//                             ->where('id_bank_tujuan', $bank->id_bank_tujuan);
                        
//                         // Apply sumber dana filter
//                         if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//                             $bankQuery->whereIn('id_sumber_dana', $sumberDanaIds);
//                         }
                        
//                         $saldo = $bankQuery->sum(DB::raw('debet - kredit'));

//                         $rekap[] = [
//                             'bank' => $bank->nama_tujuan,
//                             'saldo_va' => $saldo,
//                             'saldo_sap' => 0,
//                             'selisih' => $saldo,
//                             'keterangan' => "Saldo akhir tahun {$tahun}"
//                         ];
//                     }
//                     break;

//                 case 'va':
//                     $sumberDanaQuery = SumberDana::query();
                    
//                     // Filter hanya sumber dana yang dipilih
//                     if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//                         $sumberDanaQuery->whereIn('id_sumber_dana', $sumberDanaIds);
//                     }
                    
//                     foreach ($sumberDanaQuery->get() as $sd) {
//                         $saldo = GabunganMasukKeluar::whereYear('tanggal', $tahun)
//                             ->where('id_sumber_dana', $sd->id_sumber_dana)
//                             ->sum(DB::raw('debet - kredit'));

//                         $rekap[] = [
//                             'bank' => $sd->nama_sumber_dana,
//                             'saldo_va' => $saldo,
//                             'saldo_sap' => 0,
//                             'selisih' => $saldo,
//                             'keterangan' => "Saldo akhir tahun {$tahun}"
//                         ];
//                     }
//                     break;

//                 case 'kategori-full':
//                     $dataKategoriQuery = GabunganMasukKeluar::select(
//                         'kategori_kriteria.nama_kriteria as kategori',
//                         'sub_kriteria.nama_sub_kriteria as sub',
//                         'item_sub_kriteria.nama_item_sub_kriteria as item',
//                         DB::raw('COALESCE(SUM(gabungan_masuk_keluars.kredit),0) as kredit')
//                     )
//                     ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
//                     ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'gabungan_masuk_keluars.id_sub_kriteria')
//                     ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'gabungan_masuk_keluars.id_item_sub_kriteria')
//                     ->whereYear('tanggal', $tahun);
                    
//                     // Apply sumber dana filter
//                     if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//                         $dataKategoriQuery->whereIn('gabungan_masuk_keluars.id_sumber_dana', $sumberDanaIds);
//                     }
                    
//                     $dataKategori = $dataKategoriQuery->groupBy('kategori_kriteria.nama_kriteria', 'sub_kriteria.nama_sub_kriteria', 'item_sub_kriteria.nama_item_sub_kriteria')
//                         ->orderBy('kategori_kriteria.nama_kriteria')
//                         ->get();

//                     foreach ($dataKategori as $row) {
//                         $rekap[$row->kategori][$row->sub][] = [
//                             'item' => $row->item,
//                             'kredit' => floatval($row->kredit)
//                         ];
//                     }
//                     break;
                                
//                     // Apply sumber dana filter
//                     // if ($sumberDanaIds && is_array($sumberDanaIds) && count($sumberDanaIds) > 0) {
//                     //     $rekapKategoriQuery->whereIn('gabungan_masuk_keluars.id_sumber_dana', $sumberDanaIds);
//                     // }
//                 case 'kategori':
//                     $rekapKategoriQuery = GabunganMasukKeluar::select(
//                         'kategori_kriteria.nama_kriteria as kategori',
//                         DB::raw('COALESCE(SUM(gabungan_masuk_keluars.kredit),0) as total_kredit')
//                     )
//                     ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'gabungan_masuk_keluars.id_kategori_kriteria')
//                     ->where('tipe','Masuk')
//                     ->whereYear('tanggal', $tahun);

                    
//                     $rekap = $rekapKategoriQuery->groupBy('kategori_kriteria.nama_kriteria')
//                         ->orderBy('kategori_kriteria.nama_kriteria')
//                         ->get();
//                     break;
//             }
//         }

//         return view('cash_bank.reportMasuk', [
//             'data' => $data,
//             'bankTujuanList' => BankTujuan::all(),
//             'sumberDanaList' => SumberDana::all(),
//             'tahunList' => $tahunList,
//             'kategoriList' => $kategoriList,
//             'selectedBankTujuan' => $bankTujuanId,
//             'selectedSumberDana' => $sumberDanaIds, // Sekarang array
//             'selectedKategori' => $kategoriIds, // Sekarang array
//             'selectedTahun' => $tahun,
//             'rekapVA' => $rekap,
//             'rekapanVA' => $rekapanVA,
//             'agendaData' => $agendaData,
//             'rekapJenisPembayaran' => $rekapJenisPembayaran,
//             'selectedJenis' => $filterJenis,
            
//         ]);
//     }

// <?php

// namespace App\Http\Controllers;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use App\Models\BankMasuk;
// use App\Models\BankTujuan;
// use App\Models\SumberDana;
// use App\Models\jenisPemabayaran;
// use App\Models\KategoriKriteria;
// use App\Imports\importExcelMasukk;
// use Maatwebsite\Excel\Facades\Excel;

class BankMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->keyword;

        $data = BankMasuk::with(['sumberDana','bankTujuan','kategori','jenisPembayaran'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('agenda_tahun','like',"%{$search}%")
                      ->orWhere('penerima','like',"%{$search}%")
                      ->orWhere('uraian','like',"%{$search}%")
                      ->orWhere('tanggal','like',"%{$search}%")
                      ->orWhereHas('sumberDana', fn($x) =>
                            $x->where('nama_sumber_dana','like',"%{$search}%"))
                      ->orWhereHas('bankTujuan', fn($x) =>
                            $x->where('nama_tujuan','like',"%{$search}%"))
                      ->orWhereHas('kategori', fn($x) =>
                            $x->where('nama_kriteria','like',"%{$search}%"))
                      ->orWhereHas('jenisPembayaran', fn($x) =>
                            $x->where('nama_jenis_pembayaran','like',"%{$search}%"));
                });
            })
            ->orderByDesc('tanggal')
            ->orderBy('created_at')
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
            'id_sub_kriteria' => $request->id_sub_kriteria,
            'id_item_sub_kriteria' => $request->id_item_sub_kriteria,
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

    public function report(Request $request)
    {
        $search = $request->keyword;
        $tahun = $request->tahun;
        $bankTujuanId = $request->bank_tujuan;
        $sumberDanaIds = $request->sumber_dana;
        $bulan         = $request->bulan;
        $tglAwal       = $request->tanggal_awal;
        $tglAkhir      = $request->tanggal_akhir;
        $bankTujuanId  = $request->bank_tujuan;
        $sumberDanaIds = $request->sumber_dana;
        $jenisPembayaranIds   = $request->jenis_pembayaran;

    /* ================= FILTER TANGGAL ================= */
    $filterTanggal = function ($q) use ($tglAwal, $tglAkhir, $tahun, $bulan) {
        if ($tglAwal && $tglAkhir) {
            $q->whereBetween('tanggal', [$tglAwal, $tglAkhir]);
        } elseif ($tahun && $bulan) {
            $q->whereYear('tanggal', $tahun)
              ->whereMonth('tanggal', $bulan);
        } elseif ($tahun) {
            $q->whereYear('tanggal', $tahun);
        }
    };

        $tahunList = BankMasuk::select(DB::raw('YEAR(tanggal) as tahun'))
            ->groupBy(DB::raw('YEAR(tanggal)'))
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $query = BankMasuk::with(['bankTujuan','sumberDana','kategori','jenisPembayaran'])
            ->orderBy('tanggal','asc');

        if ($tahun) {
            $query->whereYear('tanggal',$tahun);
        }

        if ($bankTujuanId) {
            $query->where('id_bank_tujuan',$bankTujuanId);
        }

        if ($sumberDanaIds && is_array($sumberDanaIds)) {
            $query->whereIn('id_sumber_dana',$sumberDanaIds);
        }
        if ($jenisPembayaranIds && is_array($jenisPembayaranIds)) {
            $query->whereIn('id_jenis_pembayaran',$jenisPembayaranIds);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('agenda_tahun','like',"%{$search}%")
                  ->orWhere('uraian','like',"%{$search}%")
                  ->orWhere('penerima','like',"%{$search}%")
                  ->orWhere('jenisPembayaran','like',"%{$search}%")
                  ->orWhereHas('bankTujuan', fn($x)=>
                        $x->where('nama_tujuan','like',"%{$search}%"));
            });
        }

        $data = $query->get();

        // saldo berjalan
        $saldoAkhir = 0;
        foreach ($data as $row) {
            $saldoAkhir += ($row->debet ?? 0) - ($row->kredit ?? 0);
            $row->saldo_akhir = $saldoAkhir;
        }

        $rekapJenisPembayaran = DB::table('bank_masuk')
        ->join('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_masuk.id_jenis_pembayaran')
        ->select(
            'jenis_pembayarans.id_jenis_pembayaran',
            'jenis_pembayarans.nama_jenis_pembayaran'
        )
        ->whereNotNull('bank_masuk.id_jenis_pembayaran')
        ->distinct()
        ->orderBy('jenis_pembayarans.nama_jenis_pembayaran')
        ->get();

    /* ================= KATEGORI ================= */
        $kategoriList = DB::table('bank_masuk')
        ->join('kategori_kriteria','kategori_kriteria.id_kategori_kriteria','=','bank_masuk.id_kategori_kriteria')
        ->select(
            'kategori_kriteria.id_kategori_kriteria',
            'kategori_kriteria.nama_kriteria',
            DB::raw('SUM(bank_masuk.debet) as total_kredit')
        )
        ->where($filterTanggal)
        ->where('tipe','Masuk')
        ->when($sumberDanaIds, fn($q)=>$q->whereIn('bank_masuk.id_sumber_dana',$sumberDanaIds))
        ->groupBy('kategori_kriteria.id_kategori_kriteria','kategori_kriteria.nama_kriteria')
        ->get();


        return view('cash_bank.reportMasuk', [
            'data' => $data,
            'tahunList' => $tahunList,
            'bankTujuanList' => BankTujuan::all(),
            'sumberDanaList' => SumberDana::all(),
            'rekapJenisPembayaran' => JenisPembayaran::all(),
            'kategoriList' => KategoriKriteria::where('tipe','Masuk')->get(),
            'selectedTahun' => $tahun,
            'selectedBankTujuan' => $bankTujuanId,
            'selectedSumberDana' => $sumberDanaIds,
            'selectedJenisPembayaran' => $jenisPembayaranIds,
        ]);
    }
    // $request->validate([
    //     'fileExcel' => 'required|mimes:xlsx,xls'
    // ]);
    // Excel::queueImport(new importExcelMasukImport, $request->file('fileExcel'));
//     try {
//         Excel::import(new importExcelMasukImport, $request->file('fileExcel'));
//         // dd('IMPORT DIJALANKAN');
//     } catch (\Throwable $e) {
//         return back()->with('error', $e->getMessage());
//     }

//     return redirect()
//         ->route('bank-masuk.index')
//         ->with('success', 'Data berhasil diimport');

    // public function importExcel(Request $request)
    // {
    //     $request->validate([
    //         'fileExcel' => 'required|mimes:xlsx,xls'
    //     ]);
    //     ini_set('memory_limit', '-1');
    //     set_time_limit(0);

    //     $file = $request->file('fileExcel')->store('public/import');

    //     Excel::import(new importMasuk, $file);

    //     return redirect()
    //         ->route('bank-masuk.index')
    //         ->with('success', 'Data berhasil diimport');
    // }
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