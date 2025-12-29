<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;

class GabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    
    {
        $agendaData = Dokumen::select(
        'id', 
        DB::raw("CONCAT(nomor_agenda,'_',tahun) as agenda_tahun"),
        'uraian_spp',
        'nilai_rupiah',
        'dibayar_kepada'
        )->get();

        // request
        $search = $request->keyword;
        $data = BankKeluar::when($search, function($query, $search) {

        // kolom langsung dari tabel
            $query->where(function($q) use ($search) {
                $q->where('tanggal_keluar', 'like', "%{$search}%")
                ->orWhere('agenda_tahun', 'like', "%{$search}%")
                ->orWhere('penerima', 'like', "%{$search}%")
                ->orWhere('nilai_rupiah', 'like', "%{$search}%")
                ->orWhere('debet', 'like', "%{$search}%")
                ->orWhere('kredit', 'like', "%{$search}%")
                ->orWhere('uraian', 'like', "%{$search}%");
            });

            // sumber dana
            $query->orWhereHas('sumberDana', function($q) use ($search) {
                $q->where('nama_sumber_dana', 'like', "%{$search}%");
            });

            // bank tujuan
            $query->orWhereHas('bankTujuan', function($q) use ($search) {
                $q->where('nama_tujuan', 'like', "%{$search}%");
            });

            // kategori
            $query->orWhereHas('kategori', function($q) use ($search) {
                $q->where('nama_kriteria', 'like', "%{$search}%");
            });

            // sub kriteria
            $query->orWhereHas('subKriteria', function($q) use ($search) {
                $q->where('nama_sub_kriteria', 'like', "%{$search}%");
            });

            // item sub kriteria
            $query->orWhereHas('itemSubKriteria', function($q) use ($search) {
                $q->where('nama_item_sub_kriteria', 'like', "%{$search}%");
            });

        })->latest()->get();

        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
