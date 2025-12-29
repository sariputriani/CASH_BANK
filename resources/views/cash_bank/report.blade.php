@extends('layouts/index')
@section('content')

<div class="container-fluid mt-3">

    <!-- Title -->
    <h1 class="tittle">Report <span style="color: #FF7518">Bank</span></h1>
    <small>Ini data Report keseluruhan BANK PTPN IV Regional V</small>

    <!-- Search & Action Bar -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex flex-wrap align-items-center gap-2">

                    <!-- Search Box -->
                    <div class="input-group search-box w-100 w-md-50">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input 
                            type="text" 
                            class="form-control border-start-0" 
                            placeholder="Search Here...">
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2">

                        <!-- Filter -->
                        <button class="btn btn-outline-secondary rounded-3 d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-funnel"></i>
                            <span>Filter</span>
                        </button>

                        <!-- Export PDF -->
                        <button class="btn btn-outline-danger rounded-3 d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-filetype-pdf"></i>
                            <span>Export PDF</span>
                        </button>

                        <!-- Export Excel -->
                        <button class="btn btn-outline-success rounded-3 d-flex align-items-center gap-2 px-3">
                            <i class="bi bi-file-earmark-excel"></i>
                            <span>Export Excel</span>
                        </button>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">

                    <h5 class="tittle mb-3">Report RK Bank <span style="color: #f83200">305</span></h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Agenda</th>
                                    <th>Tanggal</th>
                                    <th>Sumber Dana</th>
                                    <th>Bank Tujuan</th>
                                    <th>Kriteria CF</th>
                                    <th>Penerima</th>
                                    <th>Uraian</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @for($i = 1; $i <= 6; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>001_2025</td>
                                    <td>01/01/2025</td>
                                    <td>PT. Bank Mandiri (Collection Account) - 146-00-0443935-7</td>
                                    <td>-</td>
                                    <td>Lain - Lain</td>
                                    <td>-</td>
                                    <td>CICILAN TUNAI APRIL AN M.REGINA CA Cash Deposit</td>
                                    <td>400.000</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                                @endfor
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection
