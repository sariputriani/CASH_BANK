@extends("layouts/index")
@section('content')

<div class="container-fluid mt-4">
    <h1 class="tittle">Daftar <span style="color: #FF7518">Bank Tujuan</span></h1>
    <small>Ini daftar Bank PTPN</small>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-content-center">
                        <form action="{{ route('daftarBank.index')}}" class="w-100">
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
                            data-bs-target="#ModalTambahBank">
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
                    <h5>Daftar Nama Bank Tujuan</h5>
                    <hr>
                    <table class="table table-bordered table-striped justify-content-between align-content-center">
                        <thead class="text-center table-primary ">
                            <tr>
                                <th>No</th>
                                <th>Nama Bank Tujuan</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Update</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse ($data as $index => $row)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $row->nama_tujuan }}</td>
                                <td class="text-center">{{ $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-center">{{ $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-center">
                                       <button class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBankTujuan"
                                            data-id="{{ $row->id_bank_tujuan }}"
                                            data-nama="{{ $row->nama_tujuan }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('daftarBank.destroy', $row->id_bank_tujuan) }}"
                                        method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        @endforelse
                                
                            
                        </tbody>
                    </table>
                    @include('cash_bank.modal.editBankTujuan')

                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CREATE & EDIT --}}
@include('cash_bank.modal.tambahBank')


@endsection