@extends('layouts/index')
@section('content')

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="tittle">Detail <span style="color: #FF7518">Report Transaksi</span></h1>
            <small class="text-muted">Detail transaksi berdasarkan item yang dipilih</small>
        </div>
        <a href="{{ route('bank-keluar.report', array_filter([
            'tahun' => request('tahun'),
            'bulan' => request('bulan'),
            'tanggal' => request('tanggal'),
            'bank_tujuan' => request('bank_tujuan'),
            'sumber_dana' => request('sumber_dana'),
            'id_jenis_pembayaran' => request('id_jenis_pembayaran'),
            'rekapanVA' => 'kategori-full'
        ])) }}" class="btn">
            <i class="bi bi-arrow-left"></i> Kembali ke Report
        </a>
    </div>

    @if(isset($filterInfo) && count($filterInfo) > 0)
    <div class="alert alert-info">
        <strong><i class="bi bi-funnel"></i> Filter Aktif:</strong><br>
        <small>{{ implode(' | ', $filterInfo) }}</small>
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>
                            Detail Transaksi {{ $item }}
                        </h5>
                        <div>
                            <a href="{{ route('detail-item.export_excel', [
                                'kategori' => $kategori,
                                'sub' => $sub,
                                'item' => $item,
                                'tahun' => $tahun,
                                'bulan' => request('bulan'),
                                'tanggal' => request('tanggal'),
                                'bank_tujuan' => request('bank_tujuan'),
                                'sumber_dana' => request('sumber_dana'),
                                'id_jenis_pembayaran' => request('id_jenis_pembayaran')
                            ]) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </a>
                            <a href="{{ route('detail-item.view_pdf', [
                                'kategori' => $kategori,
                                'sub' => $sub,
                                'item' => $item,
                                'tahun' => $tahun,
                                'bulan' => request('bulan'),
                                'tanggal' => request('tanggal'),
                                'bank_tujuan' => request('bank_tujuan'),
                                'sumber_dana' => request('sumber_dana'),
                                'id_jenis_pembayaran' => request('id_jenis_pembayaran')
                            ]) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                                <i class="bi bi-printer"></i> Download PDF
                            </a>
                            <!-- <a href="#" onclick="window.print()" class="btn btn-sm">
                                <i class="bi bi-printer"></i> Export PDF
                            </a> -->
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        @include('cash_bank.exportExcel.detailItem')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden; 
    }

    #printArea, #printArea * {
        visibility: visible; 
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

@endsection