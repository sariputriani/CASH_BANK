@extends('layouts/index')
@section('content')

<div class="container-fluid mt-3">

    <!-- Title -->
    <h1 class="tittle">Bank<span style="color: #FF7518"> Masuk</span></h1>
    <small>Ini data Bank Masuk (Uang Masuk) PTPN IV Regional V</small>

    <!-- Search + Add Button -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <!-- Search -->
                   <form action="{{ route('bank-masuk.index') }}" method="GET" class="w-100">
                        <div class="input-group search-box w-75" >
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                name="keyword" 
                                class="form-control border-start-0" placeholder="Search Here ...." value="{{ request('keyword') }}">
                        </div>
                    </form>

                    <!-- Tambah -->
                     <div class="row">
                        <div class="col-md-12 d-flex gap-2">
                            <button 
                                type="button"
                                class="btn-tambah btn-success rounded-2 shadow-sm border-0"
                                data-bs-toggle="modal"
                                data-bs-target="#ModalImportFileExcelMasuk"
                                style="background-color:red;">
                                <i class="bi bi-database-fill-add"></i> Import Excel
                            </button>
                            <a href="#" onclick="window.print()" class="btn-export " style="border-radius: 10px; padding:15px;color: white;background-color: green; width:150px;">
                                           <i class="bi bi-printer"></i> Export PDF
                                    </a>   
                            <!-- Tambah -->
                            <button 
                                class="btn-tambah btn-success rounded-2 shadow-sm border-0"
                                data-bs-toggle="modal"
                                data-bs-target="#ModalCreateMasuk">
                                <i class="bi bi-database-fill-add"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm" >
                <div class="card-body table-responsive" id="printArea">
                    <h5>Daftar Bank Masuk</h5>
                    <hr>

                    <table class="table table-bordered table-striped">
                        <thead class="text-center table-primary ">
                            <tr>
                                <th>Agenda</th>
                                <th>Tanggal Masuk</th>
                                <th>Sumber Dana</th>
                                <th>Bank Tujuan</th>
                                <th>Kriteria</th>
                                <th>Penerima</th>
                                <th>Uraian</th>
                                <th>Nilai Rupiah</th>
                                <th>Debet</th>
                                <th>Jenis Pembayaran</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $row)
                                <tr>
                                    <td>{{ $row->agenda_tahun }}</td>
                                    <td>{{ $row->tanggal }}</td>
                                    <td>{{ $row->sumberDana->nama_sumber_dana ?? '-' }}</td>
                                    <td>{{ $row->bankTujuan->nama_tujuan ?? '-' }}</td>
                                    <td>{{ $row->kategori->nama_kriteria ?? '-' }}</td>
                                    <td>{{ $row->penerima }}</td>
                                    <td>{{ $row->uraian }}</td>
                                    <td>{{ number_format($row->nilai_rupiah, 0, ',', '.') }}</td>
                                    <td>{{ 'Rp' . number_format($row->debet, 0, ',', '.') }}</td>
                                    <td>{{ $row->jenisPembayaran->nama_jenis_pembayaran ?? '-'}}</td>
                                    <td>{{ $row->keterangan}}</td>
                                    <td class="text-center">

                                        <button
                                        class="btn btn-primary btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit"

                                        data-id="{{ $row->id_bank_masuk }}"
                                        data-agenda="{{ $row->agenda_tahun }}"
                                        data-penerima="{{ $row->penerima }}"
                                        data-uraian="{{ $row->uraian }}"
                                        data-tanggal="{{ $row->tanggal }}"
                                        data-bank="{{ $row->id_bank_tujuan }}"
                                        data-sumber="{{ $row->id_sumber_dana }}"
                                        data-kategori="{{ $row->id_kategori_kriteria }}"
                                        data-jenis="{{ $row->id_jenis_pembayaran }}"
                                        data-debet="{{ $row->debet }}"
                                        >
                                        <i class="bi bi-pencil-square text-white"></i>
                                        </button>
                                     
                                        <form action="{{ route('bank-masuk.destroy', $row->id_bank_masuk) }}"
                                        method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn bg-danger text-white"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="text-center">Data yang anda cari tidak ada!!</td>
                                </tr>
                        @endforelse
                            </tbody>
                        </table>
                        @include('cash_bank.modal.edit') 
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $data->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('edit');
  const form  = document.getElementById('formEdit');

  modal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;

    form.action = '/bank-masuk/${btn.dataset.id}';

    modal.querySelector('[name="agenda_tahun"]').value = btn.dataset.agenda;
    modal.querySelector('[name="penerima"]').value     = btn.dataset.penerima;
    modal.querySelector('[name="uraian"]').value       = btn.dataset.uraian;
    modal.querySelector('[name="tanggal"]').value      = btn.dataset.tanggal;

    modal.querySelector('[name="id_bank_tujuan"]').value   = btn.dataset.bank;
    modal.querySelector('[name="id_sumber_dana"]').value   = btn.dataset.sumber;
    modal.querySelector('[name="id_kategori_kriteria"]').value = btn.dataset.kategori;
    modal.querySelector('[name="id_jenis_pembayaran"]').value  = btn.dataset.jenis;
    modal.querySelector('[name="debet"]').value  = btn.dataset.debet;
  });
});
</script>

{{-- style -- }}
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }

    /* tombol export tetap disembunyikan walau #printArea * visible */
    #printArea .btn-export {
        display: none !important;
        visibility: hidden !important;
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    #printArea .btn-export {
        background-color: #0d6efd;
        color: #ffffff;
    }
}

table-wrapper {
    max-height: 600px;
    overflow-y: scroll;
    overflow-x: scroll; /* Enable horizontal scroll */
    margin: 20px;
}

/* Checkbox column */
td:first-child, th:first-child {
    position: sticky;
    left: 0px;
    z-index: 1;
    background-color: white; /* Add background to prevent see-through */
}

/* No column */
td:nth-child(2), th:nth-child(2) {
    position: sticky;
    left: 50px; /* Adjust based on checkbox width */
    z-index: 1;
    background-color: white;
}

/* Agenda column */
td:nth-child(3), th:nth-child(3) {
    position: sticky;
    left: 100px; /* Adjust based on previous column width */
    z-index: 1;
    background-color: white;
}

/* No Bukti column */
td:nth-child(4), th:nth-child(4) {
    position: sticky;
    left: 200px; /* Adjust based on previous columns width */
    z-index: 1;
    background-color: white;
}

/* Tanggal column */
td:nth-child(5), th:nth-child(5) {
    position: sticky;
    left: 280px; /* Adjust based on previous columns width */
    z-index: 1;
    background-color: white;
}

/* Ensure headers stay on top */
th:first-child, th:nth-child(2), th:nth-child(3), th:nth-child(4), th:nth-child(5) {
    z-index: 3;
    background-color: #d4e9f7; /* Match your header background color */
}
</style>


{{-- MODAL CREATE & EDIT --}}
@include('cash_bank.modal.tambahMasuk')
@include('cash_bank.modal.importExcelMasuk')


@endsection
