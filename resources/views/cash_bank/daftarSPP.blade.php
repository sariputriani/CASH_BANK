@extends('layouts/index')
@section('content')
<div class="container-fluid mt-3">

    <!-- Title -->
    <h1 class="tittle">Daftar <span style="color: #FF7518">SPP</span></h1>
    <small>Ini daftar SPP pada Aplikasi Agenda Online</small>

    <!-- Search & Filter -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <!-- Search -->
                    <!-- <form action="{{ route('daftar-spp.index') }}" method="GET">
                        <div class="input-group search-box w-75">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                name="keyword" 
                                class="form-control border-start-0" 
                                placeholder="Search Here..."
                                value="{{ request('keyword') }}">
                        </div>
                    </form> -->
                     <div class="col-12 col-lg-6">
                                <form action="{{ route('daftar-spp.index') }}" method="GET" class="w-100">
                                    <div class="input-group search-box w-75" >
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" 
                                            name="search" 
                                            class="form-control border-start-0" placeholder="Search Here ...." value="{{ request('search') }}" autofocus>
                                    </div>
                                </form>
                        </div>

                    <!-- Filter -->
                    <div class="d-flex gap-2 flex-wrap">
                       <a href="?status=belum" class="btn btn-outline-warning">Belum Siap Bayar</a>
                        <a href="?status=siap" class="btn btn-outline-primary">Siap Bayar</a>
                        <a href="?status=sudah" class="btn btn-outline-success">Sudah Dibayar</a>
                        <a href="?status=" class="btn btn-light">Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="tittle mb-3">Daftar SPP</h5>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped">
                            <thead class="text-center table-primary ">
                                <tr>
                                    <th>No</th>
                                    <th>Nomor_Agenda</th>
                                    <th>SPP</th>
                                    <th>Tanggal SPP</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal SPK</th>
                                    <th>Tanggal BA</th>
                                    <th>Dibayar Kepada</th>
                                    <th>Uraian SPP</th>
                                    <th>Nilai Rupiah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody >
                                @forelse ($data as $index => $row)
                                <tr >
                                        <td class="sticky-col sticky-no text-center">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $row->nomor_agenda }}</td>
                                        <td>{{ $row-> nomor_spp}}</td>
                                        <td>{{ $row->tanggal_spp }}</td>
                                        <td>{{ $row->tanggal_masuk }}</td>
                                        <td>{{ $row->tanggal_spk }}</td>
                                        <td>{{ $row->tanggal_berita_acara }}</td>
                                        <td>{{ $row->dibayar_kepada }}</td>
                                        <td>{{ $row->uraian_spp }}</td>
                                        <td>{{ 'Rp' . number_format($row->nilai_rupiah, 0, ',', '.') }}</td>
                                        <td>{{ $row->status_pembayaran}}</td>
                                        
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center">Data yang anda cari tidak ada!!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4 d-flex justify-content-end">
                            {{ $data->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
