<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class sap implements FromView
{
    /**
    * @return \Illuminate\Support\View
    */
    protected $request;
    
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view() : View
    {
        $bankTujuanId = auth()->user()->id_bank_tujuan;
        $tahun = $this->request->tahun ?? date('Y');
        $tahunDipilih = $this->request->tahun ?? date('Y');

        $listTahun = DB::table('bank_keluars')
            ->selectRaw('YEAR(tanggal) as tahun')
            ->where('id_bank_tujuan', $bankTujuanId)
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // ================= UNION TRANSAKSI =================
        $trx = DB::query()->fromSub(function ($q) use ($bankTujuanId, $tahun) {

            // -------- BANK MASUK (DEBET) --------
            $q->select(
                'bank_masuk.tanggal',
                'bank_masuk.agenda_tahun as agenda',
                'bank_masuk.id_bank_tujuan',
                'bank_masuk.penerima',
                'bank_masuk.uraian',
                DB::raw('bank_masuk.debet'),
                DB::raw('0 as kredit'),
                DB::raw('NULL as no_sap'),
                DB::raw('NULL as id_bank_keluar')
            )
            ->from('bank_masuk')
            ->where('bank_masuk.id_bank_tujuan', $bankTujuanId)
            ->whereYear('bank_masuk.tanggal', $tahun)

            ->unionAll(

                // -------- BANK KELUAR (KREDIT) --------
                DB::table('bank_keluars')
                    ->select(
                        'bank_keluars.tanggal',
                        'bank_keluars.agenda_tahun as agenda',
                        'bank_keluars.id_bank_tujuan',
                        'bank_keluars.penerima',
                        'bank_keluars.uraian',
                        DB::raw('0 as debet'),
                        DB::raw('bank_keluars.kredit'),
                        'bank_keluars.no_sap',
                        'bank_keluars.id_bank_keluar'
                    )
                    ->where('bank_keluars.id_bank_tujuan', $bankTujuanId)
                    ->whereYear('bank_keluars.tanggal', $tahun)
            );
        }, 'trx');

        // ================= JOIN BANK TUJUAN =================
        $rows = DB::table(DB::raw("({$trx->toSql()}) as trx"))
            ->mergeBindings($trx)
            ->leftJoin(
                'bank_tujuan',
                'bank_tujuan.id_bank_tujuan',
                '=',
                'trx.id_bank_tujuan'
            )
            ->select(
                'trx.*',
                'bank_tujuan.nama_tujuan'
            )
            ->orderBy('trx.tanggal')
            ->get();

        // ================= SALDO BERJALAN =================
        $saldo = 0;
        foreach ($rows as $row) {
            $saldo += ($row->debet ?? 0) - ($row->kredit ?? 0);
            $row->saldo_akhir = $saldo;
        }

        return view('cash_bank.exportExcel.userSap', [
            'data'  => $rows,
            'tahun'       => $listTahun,
            'tahunAktif'  => $tahunDipilih
        ]);
    }
}
