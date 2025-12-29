@extends("layouts/index")
@section('content')

<div class="container-fluid mt-4">
    <h1 class="tittle">Daftar <span style="color: #FF7518">Nomor Rekening</span></h1>
    <small>Ini daftar nomor rekening bankr</small>
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
                            data-bs-target="#ModalTambahRekening">
                            <i class="bi bi-database-fill-add"></i> Tambah
                        </button>
                       

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
                    <h5>Daftar Nama Bank </h5>
                    <hr>
                    <table class="table table-bordered table-striped justify-content-between align-content-center">
                        <thead class="text-center table-primary ">
                            <tr>
                                <th>No</th>
                                <th>Nama Bank</th>
                                <th>Nomor Rekening</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse ($data as $row)
                            <tr >
                                    <td>{{ $row->id_rekening}}</td>
                                    <td>{{ $row->bank->nama_bank ?? '-' }}</td>
                                    <td>{{ $row->nomor_rekening }}</td>
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
@include('cash_bank.modal.tambahRekening')

@endsection