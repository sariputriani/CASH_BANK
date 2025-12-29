<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\BankKeluar;
use App\Models\BankTujuan;
use App\Models\SumberDana;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use App\Models\DokumenAgenda;
use App\Imports\EmployeeImport;
use App\Models\ItemSubKriteria;
use App\Models\JenisPembayaran;
use App\Models\KategoriKriteria;
use Illuminate\Support\Facades\DB;
use App\Models\GabunganMasukKeluar;
use Maatwebsite\Excel\Facades\Excel;
// use Maatwebsite\Excel\Excel;

class BankKeluarController extends Controller
{
   
    public function index(Request $request)
    {
        $agenda = DB::connection('mysql_agenda_online')
            ->table('dokumens')
            ->select(
                'id as dokumen_id',
                DB::raw("CONCAT(nomor_agenda,'_',tahun) as agenda_tahun"),
                'uraian_spp as uraian',
                'nilai_rupiah',
                'dibayar_kepada as penerima',
                'jenis_pembayaran'
            )->where('status_pembayaran', 'SIAP DIBAYAR')
            ->get();

        $data = BankKeluar::latest()->get();

        return view('cash_bank.bankKeluar', [
            'data' => $data,
            'agenda' => $agenda, 
            'sumberDana' => SumberDana::all(),
            'bankTujuan' => BankTujuan::all(),
            'kategoriKriteria' => KategoriKriteria::where('tipe','Keluar')->get(),
            'subKriteria' => SubKriteria::all(),
            'itemSubKriteria' => ItemSubKriteria::all(),
            'jenisPembayaran' => JenisPembayaran::all(),
        ]);
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

//     public function report(Request $request)
// {
//     /* ================= REQUEST ================= */
//     $search        = $request->keyword;
//     $tahun         = $request->tahun;
//     $bulan         = $request->bulan;
//     $tglAwal       = $request->tanggal_awal;
//     $tglAkhir      = $request->tanggal_akhir;
//     $bankTujuanId  = $request->bank_tujuan;
//     $sumberDanaIds = $request->sumber_dana;
//      $rekapanVA     = $request->rekapanVA;
//     $filterJenis   = $request->jenis_pembayaran;

//     /* ================= FILTER TANGGAL ================= */
//     $filterTanggal = function ($q) use ($tglAwal, $tglAkhir, $tahun, $bulan) {
//         if ($tglAwal && $tglAkhir) {
//             $q->whereBetween('tanggal', [$tglAwal, $tglAkhir]);
//         } elseif ($tahun && $bulan) {
//             $q->whereYear('tanggal', $tahun)
//               ->whereMonth('tanggal', $bulan);
//         } elseif ($tahun) {
//             $q->whereYear('tanggal', $tahun);
//         }
//     };

//     /* ================= DROPDOWN ================= */
//     $tahunList = collect()
//         ->merge(DB::table('bank_masuk')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
//         ->merge(DB::table('bank_keluars')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
//         ->unique()->sortDesc()->values();

//     $bulanList = collect()
//         ->merge(
//             DB::table('bank_masuk')
//                 ->when($tahun, fn($q)=>$q->whereYear('tanggal',$tahun))
//                 ->selectRaw('MONTH(tanggal) as bulan')->pluck('bulan')
//         )
//         ->merge(
//             DB::table('bank_keluars')
//                 ->when($tahun, fn($q)=>$q->whereYear('tanggal',$tahun))
//                 ->selectRaw('MONTH(tanggal) as bulan')->pluck('bulan')
//         )
//         ->unique()->sort()->values();

//     $tanggalList = DB::table('bank_keluars')
//         ->selectRaw('DATE(tanggal) as tanggal')
//         ->when($tahun, fn($q)=>$q->whereYear('tanggal',$tahun))
//         ->when($bulan, fn($q)=>$q->whereMonth('tanggal',$bulan))
//         ->groupBy('tanggal')
//         ->orderBy('tanggal')
//         ->pluck('tanggal');

//     /* ================= QUERY BANK MASUK ================= */
//     /* ================= QUERY BANK MASUK ================= */
// $bankMasuk = DB::table('bank_masuk')
//     ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_masuk.id_sumber_dana')
//     ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_masuk.id_bank_tujuan')
//     ->leftJoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_masuk.id_kategori_kriteria')
//     ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_masuk.id_sub_kriteria')
//     ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_masuk.id_item_sub_kriteria')
//     ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_masuk.id_jenis_pembayaran')
//     ->select(
//         'bank_masuk.agenda_tahun',
//         'bank_masuk.id_sumber_dana',
//         'sumber_dana.nama_sumber_dana',
//         'bank_masuk.id_bank_tujuan',
//         'bank_tujuan.nama_tujuan',
//         'bank_masuk.id_kategori_kriteria',
//         'kategori_kriteria.nama_kriteria',
//         'bank_masuk.id_sub_kriteria',
//         'sub_kriteria.nama_sub_kriteria',
//         'bank_masuk.id_item_sub_kriteria',
//         'item_sub_kriteria.nama_item_sub_kriteria',
//         'bank_masuk.uraian',
//         'bank_masuk.penerima',
//         'bank_masuk.id_jenis_pembayaran',
//         'bank_masuk.debet',
//         DB::raw('0 as kredit'),
//         'bank_masuk.tanggal',
//         'bank_masuk.keterangan',
//         'bank_masuk.created_at',
//         DB::raw('bank_masuk.id_bank_masuk as urut_id'),
//         DB::raw("'MASUK' as jenis")
//     )
//     ->where(function($q) use ($filterTanggal) {  // ✅ GUNAKAN CLOSURE
//         $filterTanggal($q);
//     })
//     ->when($filterJenis === '_null', fn($q)=>$q->whereNull('bank_masuk.id_jenis_pembayaran'))
//     ->when($filterJenis && $filterJenis !== '_null',
//         fn($q)=>$q->where('bank_masuk.id_jenis_pembayaran',$filterJenis)
//     );

// /* ================= QUERY BANK KELUAR ================= */
// $bankKeluar = DB::table('bank_keluars')
//     ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_keluars.id_sumber_dana')
//     ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_keluars.id_bank_tujuan')
//     ->leftJoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
//     ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
//     ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
//     ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_keluars.id_jenis_pembayaran')
//     ->select(
//         'bank_keluars.agenda_tahun',
//         'bank_keluars.id_sumber_dana',
//         'sumber_dana.nama_sumber_dana',
//         'bank_keluars.id_bank_tujuan',
//         'bank_tujuan.nama_tujuan',
//         'bank_keluars.id_kategori_kriteria',
//         'kategori_kriteria.nama_kriteria',
//         'bank_keluars.id_sub_kriteria',
//         'sub_kriteria.nama_sub_kriteria',
//         'bank_keluars.id_item_sub_kriteria',
//         'item_sub_kriteria.nama_item_sub_kriteria',
//         'bank_keluars.uraian',
//         'bank_keluars.penerima',
//         'bank_keluars.tanggal',
//         DB::raw('0 as debet'),
//         'bank_keluars.kredit',
//         'bank_keluars.keterangan',
//         'bank_keluars.id_jenis_pembayaran',
//         'bank_keluars.created_at',
//         DB::raw('bank_keluars.id_bank_keluar as urut_id'),
//         DB::raw("'KELUAR' as jenis")
//     )
//     ->where(function($q) use ($filterTanggal) {  // ✅ GUNAKAN CLOSURE
//         $filterTanggal($q);
//     })
//     ->when($filterJenis === '_null', fn($q)=>$q->whereNull('bank_keluars.id_jenis_pembayaran'))
//     ->when($filterJenis && $filterJenis !== '_null',
//         fn($q)=>$q->where('bank_keluars.id_jenis_pembayaran',$filterJenis)
//     );
//     /* ================= FILTER TAMBAHAN ================= */
//     if ($bankTujuanId) {
//         $bankMasuk->where('bank_masuk.id_bank_tujuan',$bankTujuanId);
//         $bankKeluar->where('bank_keluars.id_bank_tujuan',$bankTujuanId);
//     }

//     if ($sumberDanaIds) {
//         $bankMasuk->whereIn('bank_masuk.id_sumber_dana',$sumberDanaIds);
//         $bankKeluar->whereIn('bank_keluars.id_sumber_dana',$sumberDanaIds);
//     }

//     /* ================= GABUNG + ORDER (WAJIB) ================= */
//     $data = DB::query()
//     ->fromSub($bankMasuk->unionAll($bankKeluar), 'trx')
//     ->orderBy('tanggal', 'ASC')
//     ->orderBy('created_at', 'ASC')
//     ->orderBy('urut_id', 'ASC')
//     ->get();


//     /* ================= SEARCH ================= */
//     if ($search) {
//         $data = $data->filter(fn($d) =>
//             str_contains(strtolower($d->uraian ?? ''), strtolower($search)) ||
//             str_contains(strtolower($d->penerima ?? ''), strtolower($search)) ||
//             str_contains(strtolower($d->nama_tujuan ?? ''), strtolower($search)) ||
//             str_contains(strtolower($d->nama_sumber_dana ?? ''), strtolower($search)) || 
//             str_contains(strtolower($d->nama_item_sumber_dana ?? ''), strtolower($search)) || 
//             str_contains(strtolower($d->nama_jenis_pembayaran ?? ''), strtolower($search)) 
//             // str_contains(strtolower($d->nama_jenis_pembayaran ?? ''), strtolower($search)) 
//         );
//     }

//     /* ================= SALDO BERJALAN ================= */
//     $saldo = 0;
//     foreach ($data as $row) {
//         $saldo += ($row->debet ?? 0) - ($row->kredit ?? 0);
//         $row->saldo_akhir = $saldo;
//     }

//     /* ================= AGENDA BANK KELUAR ================= */
//     $agendaData = DB::table('bank_keluars')
//         ->where($filterTanggal)
//         ->where('kredit','>',0)
//         ->when($sumberDanaIds, fn($q)=>$q->whereIn('id_sumber_dana',$sumberDanaIds))
//         ->when($filterJenis === '_null', fn($q)=>$q->whereNull('id_jenis_pembayaran'))
//         ->when($filterJenis && $filterJenis !== '_null',
//             fn($q)=>$q->where('id_jenis_pembayaran',$filterJenis))
//         ->orderBy('agenda_tahun')
//         ->get();

//     /* ================= JENIS PEMBAYARAN ================= */
//    $rekapJenisPembayaran = DB::table('bank_masuk')
//         ->join('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_masuk.id_jenis_pembayaran')
//         ->select(
//             'jenis_pembayarans.id_jenis_pembayaran',
//             'jenis_pembayarans.nama_jenis_pembayaran'
//         )
//         ->whereNotNull('bank_masuk.id_jenis_pembayaran')
//         ->distinct()
//         ->orderBy('jenis_pembayarans.nama_jenis_pembayaran')
//         ->get();
    

//     /* ================= KATEGORI ================= */
//     $kategoriList = DB::table('bank_keluars')
//         ->join('kategori_kriteria','kategori_kriteria.id_kategori_kriteria','=','bank_keluars.id_kategori_kriteria')
//         ->select(
//             'kategori_kriteria.id_kategori_kriteria',
//             'kategori_kriteria.nama_kriteria',
//             DB::raw('SUM(bank_keluars.kredit) as total_kredit')
//         )
//         ->where($filterTanggal)
//         ->when($sumberDanaIds, fn($q)=>$q->whereIn('bank_keluars.id_sumber_dana',$sumberDanaIds))
//         ->groupBy('kategori_kriteria.id_kategori_kriteria','kategori_kriteria.nama_kriteria')
//         ->get();

//     /* ================= REKAP ================= */

//     $rekap = [];

//     if ($rekapanVA) {
//         switch ($rekapanVA) {

//             /* ===== REKAP BANK ===== */
//             case 'bank':
//                 foreach (BankTujuan::all() as $bank) {

//                     $bankQuery = DB::table('bank_keluars')
//                         ->where('id_bank_tujuan', $bank->id_bank_tujuan)
//                         ->where(function ($q) use ($filterTanggal) {
//                             $filterTanggal($q);
//                         });

//                     if (!empty($sumberDanaIds)) {
//                         $bankQuery->whereIn('id_sumber_dana', $sumberDanaIds);
//                     }

//                     $saldo = $bankQuery->sum(DB::raw('debet - kredit'));

//                     $rekap[] = [
//                         'bank' => $bank->nama_tujuan,
//                         'saldo_va' => $saldo,
//                         'saldo_sap' => 0,
//                         'selisih' => $saldo,
//                         'keterangan' => 'Saldo akhir periode yang dipilih'
//                     ];
//                 }
//                 break;

//             /* ===== REKAP VA / SUMBER DANA ===== */
//             case 'va':
//                 $sdQuery = SumberDana::query();

//                 if (!empty($sumberDanaIds)) {
//                     $sdQuery->whereIn('id_sumber_dana', $sumberDanaIds);
//                 }

//                 foreach ($sdQuery->get() as $sd) {

//                     $saldo = DB::table('bank_keluars')
//                         ->where('id_sumber_dana', $sd->id_sumber_dana)
//                         ->where(function ($q) use ($filterTanggal) {
//                             $filterTanggal($q);
//                         })
//                         ->sum(DB::raw('debet - kredit'));

//                     $rekap[] = [
//                         'bank' => $sd->nama_sumber_dana,
//                         'saldo_va' => $saldo,
//                         'saldo_sap' => 0,
//                         'selisih' => $saldo,
//                         'keterangan' => 'Saldo akhir periode yang dipilih'
//                     ];
//                 }
//                 break;

//             /* ===== REKAP KATEGORI FULL ===== */
//             case 'kategori-full':
//                 $dataKategoriQuery = DB::table('bank_keluars')
//                     ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
//                     ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
//                     ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
//                     ->select(
//                         'kategori_kriteria.nama_kriteria as kategori',
//                         'sub_kriteria.nama_sub_kriteria as sub',
//                         'item_sub_kriteria.nama_item_sub_kriteria as item',
//                         DB::raw('COALESCE(SUM(bank_keluars.kredit),0) as kredit')
//                     )
//                     ->where(function ($q) use ($filterTanggal) {
//                         $filterTanggal($q);
//                     });

//                 if (!empty($sumberDanaIds)) {
//                     $dataKategoriQuery->whereIn('bank_keluars.id_sumber_dana', $sumberDanaIds);
//                 }

//                 if ($bankTujuanId) {
//                     $dataKategoriQuery->where('bank_keluars.id_bank_tujuan', $bankTujuanId);
//                 }

//                 if ($filterJenis === '_null') {
//                     $dataKategoriQuery->whereNull('bank_keluars.id_jenis_pembayaran');
//                 } elseif ($filterJenis) {
//                     $dataKategoriQuery->where('bank_keluars.id_jenis_pembayaran', $filterJenis);
//                 }

//                 $dataKategori = $dataKategoriQuery
//                     ->groupBy(
//                         'kategori_kriteria.nama_kriteria',
//                         'sub_kriteria.nama_sub_kriteria',
//                         'item_sub_kriteria.nama_item_sub_kriteria'
//                     )
//                     ->orderBy('kategori_kriteria.nama_kriteria')
//                     ->get();

//                 foreach ($dataKategori as $row) {
//                     $rekap[$row->kategori][$row->sub][] = [
//                         'item' => $row->item,
//                         'kredit' => (float) $row->kredit
//                     ];
//                 }
//                 break;
//         }
//     }



//     /* ================= RETURN VIEW ================= */
//     return view('cash_bank.reportKeluar', [
//         'data'                 => $data,
//         'tahunList'            => $tahunList,
//         'bulanList'            => $bulanList,
//         'tanggalList'          => $tanggalList,
//         'bankTujuanList'       => BankTujuan::all(),
//         'sumberDanaList'       => SumberDana::all(),
//         'jenisPembayaranList'  => jenisPembayaran::all(),
//         'kategoriList'         => KategoriKriteria::all()->where('tipe','Keluar'),
//         'agendaData'           => $agendaData,
//         'rekapJenisPembayaran' => $rekapJenisPembayaran,
//         'rekapVA'               => $rekap,
//         'rekapanVA'         => $rekapanVA,
//     ]);
// }
public function report(Request $request)
{
    /* ================= REQUEST ================= */
    $search        = $request->keyword;
    $tahun         = $request->tahun;
    $bulan         = $request->bulan;
    $tglAwal       = $request->tanggal_awal;
    $tglAkhir      = $request->tanggal_akhir;
    $bankTujuanId  = $request->bank_tujuan;
    $sumberDanaIds = $request->sumber_dana;
    $rekapanVA     = $request->rekapanVA;
    $filterJenis   = $request->jenis_pembayaran;

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

    /* ================= DROPDOWN ================= */
    $tahunList = collect()
        ->merge(DB::table('bank_masuk')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
        ->merge(DB::table('bank_keluars')->selectRaw('YEAR(tanggal) as tahun')->pluck('tahun'))
        ->unique()->sortDesc()->values();

    $bulanList = collect()
        ->merge(
            DB::table('bank_masuk')
                ->when($tahun, fn($q)=>$q->whereYear('tanggal',$tahun))
                ->selectRaw('MONTH(tanggal) as bulan')->pluck('bulan')
        )
        ->merge(
            DB::table('bank_keluars')
                ->when($tahun, fn($q)=>$q->whereYear('tanggal',$tahun))
                ->selectRaw('MONTH(tanggal) as bulan')->pluck('bulan')
        )
        ->unique()->sort()->values();

    $tanggalList = DB::table('bank_keluars')
        ->selectRaw('DATE(tanggal) as tanggal')
        ->when($tahun, fn($q)=>$q->whereYear('tanggal',$tahun))
        ->when($bulan, fn($q)=>$q->whereMonth('tanggal',$bulan))
        ->groupBy('tanggal')
        ->orderBy('tanggal')
        ->pluck('tanggal');

    /* ================= QUERY BANK MASUK ================= */
    $bankMasuk = DB::table('bank_masuk')
        ->leftJoin('sumber_dana', 'sumber_dana.id_sumber_dana', '=', 'bank_masuk.id_sumber_dana')
        ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_masuk.id_bank_tujuan')
        ->leftJoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_masuk.id_kategori_kriteria')
        ->leftJoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_masuk.id_sub_kriteria')
        ->leftJoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_masuk.id_item_sub_kriteria')
        ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_masuk.id_jenis_pembayaran')
        ->select(
            'bank_masuk.agenda_tahun',
            'bank_masuk.id_sumber_dana',
            'sumber_dana.nama_sumber_dana',
            'bank_masuk.id_bank_tujuan',
            'bank_tujuan.nama_tujuan',
            'bank_masuk.id_kategori_kriteria',
            'kategori_kriteria.nama_kriteria',
            'bank_masuk.id_sub_kriteria',
            'sub_kriteria.nama_sub_kriteria',
            'bank_masuk.id_item_sub_kriteria',
            'item_sub_kriteria.nama_item_sub_kriteria',
            'bank_masuk.uraian',
            'bank_masuk.penerima',
            'bank_masuk.id_jenis_pembayaran',
            'bank_masuk.debet',
            DB::raw('0 as kredit'),
            'bank_masuk.tanggal',
            'bank_masuk.keterangan',
            'bank_masuk.created_at',
            DB::raw('bank_masuk.id_bank_masuk as urut_id'),
            DB::raw("'MASUK' as jenis")
        );

    // Apply filter tanggal
    if ($tglAwal && $tglAkhir) {
        $bankMasuk->whereBetween('bank_masuk.tanggal', [$tglAwal, $tglAkhir]);
    } elseif ($tahun && $bulan) {
        $bankMasuk->whereYear('bank_masuk.tanggal', $tahun)
                  ->whereMonth('bank_masuk.tanggal', $bulan);
    } elseif ($tahun) {
        $bankMasuk->whereYear('bank_masuk.tanggal', $tahun);
    }

    // Apply other filters
    $bankMasuk->when($filterJenis === '_null', fn($q)=>$q->whereNull('bank_masuk.id_jenis_pembayaran'))
              ->when($filterJenis && $filterJenis !== '_null', fn($q)=>$q->where('bank_masuk.id_jenis_pembayaran',$filterJenis));

    /* ================= QUERY BANK KELUAR ================= */
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
            'bank_keluars.id_kategori_kriteria',
            'kategori_kriteria.nama_kriteria',
            'bank_keluars.id_sub_kriteria',
            'sub_kriteria.nama_sub_kriteria',
            'bank_keluars.id_item_sub_kriteria',
            'item_sub_kriteria.nama_item_sub_kriteria',
            'bank_keluars.uraian',
            'bank_keluars.penerima',
            'bank_keluars.tanggal',
            DB::raw('0 as debet'),
            'bank_keluars.kredit',
            'bank_keluars.keterangan',
            'bank_keluars.id_jenis_pembayaran',
            'bank_keluars.created_at',
            DB::raw('bank_keluars.id_bank_keluar as urut_id'),
            DB::raw("'KELUAR' as jenis")
        );

    // Apply filter tanggal
    if ($tglAwal && $tglAkhir) {
        $bankKeluar->whereBetween('bank_keluars.tanggal', [$tglAwal, $tglAkhir]);
    } elseif ($tahun && $bulan) {
        $bankKeluar->whereYear('bank_keluars.tanggal', $tahun)
                   ->whereMonth('bank_keluars.tanggal', $bulan);
    } elseif ($tahun) {
        $bankKeluar->whereYear('bank_keluars.tanggal', $tahun);
    }

    // Apply other filters
    $bankKeluar->when($filterJenis === '_null', fn($q)=>$q->whereNull('bank_keluars.id_jenis_pembayaran'))
               ->when($filterJenis && $filterJenis !== '_null', fn($q)=>$q->where('bank_keluars.id_jenis_pembayaran',$filterJenis));

    /* ================= FILTER TAMBAHAN ================= */
    if ($bankTujuanId) {
        $bankMasuk->where('bank_masuk.id_bank_tujuan',$bankTujuanId);
        $bankKeluar->where('bank_keluars.id_bank_tujuan',$bankTujuanId);
    }

    if ($sumberDanaIds) {
        $bankMasuk->whereIn('bank_masuk.id_sumber_dana',$sumberDanaIds);
        $bankKeluar->whereIn('bank_keluars.id_sumber_dana',$sumberDanaIds);
    }

    /* ================= GABUNG + ORDER ================= */
    $data = $bankMasuk->unionAll($bankKeluar)
        ->orderBy('tanggal', 'asc')
        ->orderBy('urut_id', 'asc')
        ->get();

    /* ================= SEARCH ================= */
    if ($search) {
        $data = $data->filter(fn($d) =>
            str_contains(strtolower($d->uraian ?? ''), strtolower($search)) ||
            str_contains(strtolower($d->penerima ?? ''), strtolower($search)) ||
            str_contains(strtolower($d->nama_tujuan ?? ''), strtolower($search)) ||
            str_contains(strtolower($d->nama_sumber_dana ?? ''), strtolower($search))
        );
    }

    /* ================= SALDO BERJALAN ================= */
    $saldo = 0;
     foreach ($data as $d) {
         $saldo += ($d->debet ?? 0) - ($d->kredit ?? 0);
         $d->saldo_akhir = $saldo;
     }

    /* ================= AGENDA BANK KELUAR ================= */
    $agendaData = DB::table('bank_keluars')
        ->when($tglAwal && $tglAkhir, fn($q) => $q->whereBetween('tanggal', [$tglAwal, $tglAkhir]))
        ->when($tahun && $bulan, fn($q) => $q->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan))
        ->when($tahun && !$bulan, fn($q) => $q->whereYear('tanggal', $tahun))
        ->where('kredit','>',0)
        ->when($sumberDanaIds, fn($q)=>$q->whereIn('id_sumber_dana',$sumberDanaIds))
        ->when($filterJenis === '_null', fn($q)=>$q->whereNull('id_jenis_pembayaran'))
        ->when($filterJenis && $filterJenis !== '_null', fn($q)=>$q->where('id_jenis_pembayaran',$filterJenis))
        ->orderBy('agenda_tahun')
        ->get();

    /* ================= JENIS PEMBAYARAN ================= */
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
    $kategoriList = DB::table('bank_keluars')
        ->join('kategori_kriteria','kategori_kriteria.id_kategori_kriteria','=','bank_keluars.id_kategori_kriteria')
        ->select(
            'kategori_kriteria.id_kategori_kriteria',
            'kategori_kriteria.nama_kriteria',
            DB::raw('SUM(bank_keluars.kredit) as total_kredit')
        )
        ->when($tglAwal && $tglAkhir, fn($q) => $q->whereBetween('bank_keluars.tanggal', [$tglAwal, $tglAkhir]))
        ->when($tahun && $bulan, fn($q) => $q->whereYear('bank_keluars.tanggal', $tahun)->whereMonth('bank_keluars.tanggal', $bulan))
        ->when($tahun && !$bulan, fn($q) => $q->whereYear('bank_keluars.tanggal', $tahun))
        ->when($sumberDanaIds, fn($q)=>$q->whereIn('bank_keluars.id_sumber_dana',$sumberDanaIds))
        ->groupBy('kategori_kriteria.id_kategori_kriteria','kategori_kriteria.nama_kriteria')
        ->get();

    /* ================= REKAP (existing code) ================= */
    $rekap = [];
    // ... (bagian rekap tidak perlu diubah)

    /* ================= RETURN VIEW ================= */
    return view('cash_bank.reportKeluar', [
        'data'                 => $data,
        'tahunList'            => $tahunList,
        'bulanList'            => $bulanList,
        'tanggalList'          => $tanggalList,
        'bankTujuanList'       => BankTujuan::all(),
        'sumberDanaList'       => SumberDana::all(),
        'jenisPembayaranList'  => jenisPembayaran::all(),
        'kategoriList'         => KategoriKriteria::all()->where('tipe','Keluar'),
        'agendaData'           => $agendaData,
        'rekapJenisPembayaran' => $rekapJenisPembayaran,
        'rekapVA'              => $rekap,
        'rekapanVA'            => $rekapanVA,
    ]);
}
    // Tambahkan method ini di BankKeluarController.php

    public function getDetailTransaksi(Request $request)
{
    try {
        $kategori = $request->kategori;
        $sub      = $request->sub;
        $item     = $request->item;
        $tahun    = $request->tahun;

        if (!$kategori || !$sub || !$tahun) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tidak lengkap'
            ], 400);
        }

        $data = bankKeluar::select(
                'bank_keluars.tanggal',
                'bank_keluars.agenda_tahun',
                'bank_keluars.penerima',
                'bank_keluars.uraian',
                'bank_keluars.kredit',
                'bank_keluars.id_jenis_pembayaran',
                'bank_tujuan.nama_tujuan as bank_tujuan'
            )
            ->leftjoin('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
            ->leftjoin('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
            ->leftjoin('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
            ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_keluars.id_bank_tujuan')
            ->leftJoin('jenis_pembayarans', 'jenis_pembayarans.id_jenis_pembayaran', '=', 'bank_keluars.id_jenis_pembayaran')
            ->where('kategori_kriteria.nama_kriteria', $kategori)
            ->whereYear('bank_keluars.tanggal', $tahun)
            ->when($sub !== 'ALL', function ($q) use ($sub) {
                $q->where('sub_kriteria.nama_sub_kriteria', $sub);
            })
            ->when($item && $item !== 'ALL', function ($q) use ($item) {
                $q->where('item_sub_kriteria.nama_item_sub_kriteria', $item);
            })
            ->where('bank_keluars.kredit', '>', 0)
            ->orderBy('bank_keluars.tanggal')
            ->get();

        return response()->json([
            'success' => true,
            'total_kredit' => $data->sum('kredit'),
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function exportDetailTransaksi(Request $request)
        {
            try {
                $kategori = $request->kategori;
                $sub = $request->sub;
                $item = $request->item;
                $tahun = $request->tahun;

                // Query data yang sama
                $data = bankKeluar::select(
                        'bank_keluars.tanggal',
                        'bank_keluars.agenda_tahun',
                        'bank_keluars.penerima',
                        'bank_keluars.uraian',
                        'bank_keluars.kredit',
                        'bank_tujuan.nama_tujuan as bank_tujuan'
                    
                    )
                    ->join('kategori_kriteria', 'kategori_kriteria.id_kategori_kriteria', '=', 'bank_keluars.id_kategori_kriteria')
                    ->join('sub_kriteria', 'sub_kriteria.id_sub_kriteria', '=', 'bank_keluars.id_sub_kriteria')
                    ->join('item_sub_kriteria', 'item_sub_kriteria.id_item_sub_kriteria', '=', 'bank_keluars.id_item_sub_kriteria')
                    ->leftJoin('bank_tujuan', 'bank_tujuan.id_bank_tujuan', '=', 'bank_keluars.id_bank_tujuan')
                    ->where('kategori_kriteria.nama_kriteria', $kategori)
                    ->where('sub_kriteria.nama_sub_kriteria', $sub)
                    ->where('item_sub_kriteria.nama_item_sub_kriteria', $item)
                    ->whereYear('bank_keluars.tanggal', $tahun)
                    ->where('bank_keluars.kredit', '>', 0)
                    ->orderBy('bank_keluars.tanggal', 'asc')
                    ->get();

                // Export ke Excel menggunakan Laravel Excel atau manual
                return Excel::download(new DetailTransaksiExport($data, $kategori, $sub, $item), 
                    'detail_transaksi_' . date('Ymd_His') . '.xlsx');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
            }
        }


    public function importExcel(Request $request)
    {
        $request->validate([
            'fileExcel' => 'required|mimes:xlsx,xls'
        ]);

        // Excel::import(new EmployeeImport, $request->file('fileExcel'));
        Excel::queueImport(
        new EmployeeImport,
        $request->file('fileExcel')
    );

        return redirect()
            ->route('bank-keluar.index')
            ->with('success', 'Data berhasil diimport');
    }

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
}
