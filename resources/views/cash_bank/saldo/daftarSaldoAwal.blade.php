@extends("layouts/index")
@section('content')

<div class="container-fluid mt-4">
    <h1 class="tittle">Report <span style="color: #FF7518">Saldo Awal Rekening Bank</span></h1>
    <small>Ini daftar Saldo Awal Rekening Bank</small>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body justify-content-between align-content-center d-flex">
                        <form action="" class="w-100">
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
                        </form>
                        <button 
                            class="btn-tambah btn-success rounded-2 shadow-sm border-0"
                            data-bs-toggle="modal"
                            data-bs-target="#ModalSaldoAwal">
                            <i class="bi bi-database-fill-add"></i> Tambah
                        </button>

                        <div class="d-flex gap-2">
                            <a href="" class="btn"><i class="bi bi-file-pdf-fill"></i>Export PDF</a>
                            <a href="" class="btn"><i class="bi bi-file-earmark-excel"></i>Export Excel</a>
                        </div>
                       

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body table-responsive ">
                    <h5>Daftar Saldo Awal</h5>
                    <hr>
                    <select name="" id="" class="select-control align-content-end justify-content-end">
                        <option value="" select-disble>Tahun</option>
                    </select>
                    <table class="table table-bordered table-striped justify-content-between align-content-center mt-2">
                        <thead class="text-center table-primary ">
                            <tr>
                                <th>No</th>
                                <th>Nama Bank</th>
                                <th>Nomor Rekening</th>
                                <th>Saldo Awal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse ($data as $row)
                            <tr >
                                    <td>{{ $row->id_saldo_awal}}</td>
                                    <td>{{ $row->nomor_rekening}}</td>
                                    <td>{{ $row->bank->nama_bank ?? '-' }}</td>
                                    <td>@currency($row->saldo_awal)</td>
                                    <!-- <td>{{ 'Rp'. $row->saldo_awal }}</td> -->
                                    <td>
                                        <a href="#" class="btn btn-edit"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                        <a href="#" class="btn btn-cancel"><i class="bi bi-pencil-square me-1"></i>Hapus</a>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                 <td colspan="14" class="text-center">Data yang anda cari tidak ada!!</td>
                            </tr>
                            @endforelse
                                
                            
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREATE & EDIT --}}
@include('cash_bank.modal.saldoAwal')
@endsection