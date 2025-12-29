@extends('layouts/index')
@section('content')

<div class="container-fluid mt-4 px-3 px-md-4">

    <!-- Title Section -->
    <div class="mb-4">
        <h1 class="tittle mb-2 fs-3 fs-md-2 fw-bold">Bank<span style="color: #FF7518"> Keluar</span></h1>
        <small class="text-muted d-block">Ini data Bank Keluar (Uang Keluar) PTPN IV Regional V</small>
    </div>

    <!-- Search + Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                       
                        <div class="col-12 col-lg-6">
                            <!-- <form action="{{ route('bank-keluar.index') }}" method="GET">
                                <div class="input-group search-box">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" 
                                        name="keyword" 
                                        class="form-control border-start-0 shadow-none" 
                                        placeholder="Cari data..." 
                                        value="{{ request('keyword') }}">
                                </div>
                            </form> -->
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
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="col-12 col-lg-6">
                            <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                <button class="btn bg-danger btn-sm d-flex align-items-center gap-1" id="deleteAllSelectedRecord" style="background-color:#dc3545; color: white;">
                                    <i class="bi bi-trash"></i>
                                    <span class="d-none d-sm-inline">Delete All</span>
                                </button>
                                
                                <button class="btn btn-sm d-flex align-items-center gap-1" 
                                    style="background-color:#FF7518; color: white;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalImportFileExcel">
                                    <i class="bi bi-database-fill-add"></i>
                                    <span class="d-none d-sm-inline">Import Excel</span>
                                </button>
                                
                                <button onclick="window.print()" 
                                    class="btn bg-success btn-sm d-flex align-items-center gap-1" style=" color: white;">
                                    <i class="bi bi-printer"></i>
                                    <span class="d-none d-sm-inline">Export PDF</span>
                                </button>
                                
                                <button class="btn bg-primary btn-sm d-flex align-items-center gap-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ModalCreate" style=" color: white;">
                                    <i class="bi bi-database-fill-add"></i>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-3 border-0 shadow-sm" id="printArea">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold">Daftar Bank Keluar</h5>
                    </div>
                    <hr class="mt-0">
                    
                    <!-- Desktop Table -->
                    <div class="table-responsive d-none d-xl-block">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="select_all_ids"></th>
                                    <th style="width: 50px;">No</th>
                                    <th>Agenda</th>
                                    <th>No Bukti</th>
                                    <th>Tanggal</th>
                                    <th>Sumber Dana</th>
                                    <th>Bank Tujuan</th>
                                    <th>Kriteria</th>
                                    <th>Sub Kriteria</th>
                                    <th>Item Sub</th>
                                    <th>Penerima</th>
                                    <th>Uraian</th>
                                    <th>Jenis</th>
                                    <th>Nilai (Rp)</th>
                                    <th>Kredit (Rp)</th>
                                    <th>Keterangan</th>
                                    <th style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $row)
                                <tr id="employee_ids{{ $row->id_bank_keluar}}">
                                    <td class="text-center">
                                        <input type="checkbox" name="ids" class="checkbox_ids" value="{{ $row->id_bank_keluar }}">
                                    </td>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $row->agenda_tahun }}</td>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-nowrap">{{ $row->tanggal }}</td>
                                    <td class="text-nowrap">{{ $row->sumberDana->nama_sumber_dana ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $row->bankTujuan->nama_tujuan ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $row->kategori->nama_kriteria ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $row->subKriteria->nama_sub_kriteria ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $row->itemSubKriteria->nama_item_sub_kriteria ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $row->penerima }}</td>
                                    <td>{{ $row->uraian }}</td>
                                    <td>{{ $row->jenisPembayaran->nama_jenis_pembayaran ?? '-' }}</td>
                                    <td class="text-end font-monospace">@currency($row->nilai_rupiah)</td>
                                    <td class="text-end font-monospace">@currency($row->kredit)</td>
                                    <td>{{ $row->keterangan}}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button"
                                                class="btn bg-primary btn-sm text-white"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editKeluar"
                                                data-id="{{ $row->id_bank_keluar }}"
                                                data-agenda="{{ $row->agenda_tahun }}"
                                                data-penerima="{{ $row->penerima }}"
                                                data-uraian="{{ $row->uraian }}"
                                                data-tanggal="{{ $row->tanggal }}"
                                                data-bank="{{ $row->id_bank_tujuan }}"
                                                data-sumber="{{ $row->id_sumber_dana }}"
                                                data-kategori="{{ $row->id_kategori_kriteria }}"
                                                data-jenis="{{ $row->id_jenis_pembayaran }}"
                                                data-kredit="{{ $row->kredit }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('bank-keluar.destroy', $row->id_bank_keluar) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn bg-danger btn-sm text-white"
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="17" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Data yang anda cari tidak ada
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile/Tablet Card View -->
                    <div class="d-xl-none">
                        @forelse ($data as $index => $row)
                        <div class="card mb-3 shadow-sm" id="employee_ids{{ $row->id_bank_keluar}}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input checkbox_ids" type="checkbox" 
                                            value="{{ $row->id_bank_keluar }}" name="ids">
                                        <label class="form-check-label fw-bold">
                                            #{{ $index + 1 }} - {{ $row->agenda_tahun }}
                                        </label>
                                    </div>
                                    <span class="badge bg-primary">{{ $row->tanggal }}</span>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Penerima</small>
                                        <strong>{{ $row->penerima }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Nilai Rupiah</small>
                                        <strong class="text-success">@currency($row->nilai_rupiah)</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Kredit</small>
                                        <strong class="text-info">@currency($row->kredit)</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Sumber Dana</small>
                                        <strong>{{ $row->sumberDana->nama_sumber_dana ?? '-' }}</strong>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted d-block">Uraian</small>
                                    <p class="mb-0">{{ $row->uraian }}</p>
                                </div>

                                <div class="accordion accordion-flush" id="detail{{ $row->id_bank_keluar }}">
                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed px-0 py-2" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $row->id_bank_keluar }}">
                                                <small>Detail Lainnya</small>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $row->id_bank_keluar }}" 
                                            class="accordion-collapse collapse" 
                                            data-bs-parent="#detail{{ $row->id_bank_keluar }}">
                                            <div class="accordion-body px-0">
                                                <div class="row g-2">
                                                    <div class="col-12">
                                                        <small class="text-muted">Bank Tujuan:</small>
                                                        <span>{{ $row->bankTujuan->nama_tujuan ?? '-' }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Kriteria:</small>
                                                        <span>{{ $row->kategori->nama_kriteria ?? '-' }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Sub Kriteria:</small>
                                                        <span>{{ $row->subKriteria->nama_sub_kriteria ?? '-' }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Item Sub Kriteria:</small>
                                                        <span>{{ $row->itemSubKriteria->nama_item_sub_kriteria ?? '-' }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Jenis Pembayaran:</small>
                                                        <span>{{ $row->jenisPembayaran->nama_jenis_pembayaran ?? '-' }}</span>
                                                    </div>
                                                    <div class="col-12">
                                                        <small class="text-muted">Keterangan:</small>
                                                        <span>{{ $row->keterangan }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editKeluar"
                                        data-id="{{ $row->id_bank_keluar }}"
                                        data-agenda="{{ $row->agenda_tahun }}"
                                        data-penerima="{{ $row->penerima }}"
                                        data-uraian="{{ $row->uraian }}"
                                        data-tanggal="{{ $row->tanggal }}"
                                        data-bank="{{ $row->id_bank_tujuan }}"
                                        data-sumber="{{ $row->id_sumber_dana }}"
                                        data-kategori="{{ $row->id_kategori_kriteria }}"
                                        data-jenis="{{ $row->id_jenis_pembayaran }}"
                                        data-kredit="{{ $row->kredit }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form action="{{ route('bank-keluar.destroy', $row->id_bank_keluar) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted">Data yang anda cari tidak ada</p>
                        </div>
                        @endforelse
                    </div>

                    @include('cash_bank.modal.editKeluar')
                    
                    <div class="mt-4 d-flex justify-content-end">
                        {{ $data->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editKeluar');
    const form  = document.getElementById('formEditKeluar');

    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        form.action = `/bank-keluar/${btn.dataset.id}`;

        modal.querySelector('[name="agenda_tahun"]').value = btn.dataset.agenda;
        modal.querySelector('[name="penerima"]').value     = btn.dataset.penerima;
        modal.querySelector('[name="uraian"]').value       = btn.dataset.uraian;
        modal.querySelector('[name="tanggal"]').value      = btn.dataset.tanggal;
        modal.querySelector('[name="id_bank_tujuan"]').value   = btn.dataset.bank;
        modal.querySelector('[name="id_sumber_dana"]').value   = btn.dataset.sumber;
        modal.querySelector('[name="id_kategori_kriteria"]').value = btn.dataset.kategori;
        modal.querySelector('[name="id_jenis_pembayaran"]').value  = btn.dataset.jenis;
        modal.querySelector('[name="kredit"]').value  = btn.dataset.kredit;
    });

    $(function(e){
        $("#select_all_ids").click(function(){
            $('.checkbox_ids').prop('checked',$(this).prop('checked'));
        });

        $('#deleteAllSelectedRecord').click(function(e){
            e.preventDefault();

            let all_ids = [];
            $('input[name="ids"]:checked').each(function(){
                all_ids.push($(this).val());
            });

            if(all_ids.length === 0){
                alert('Pilih data terlebih dahulu!');
                return;
            }

            if(!confirm('Yakin ingin menghapus data terpilih?')) return;

            $.ajax({
                url: "{{ route('bank-keluar.delete') }}",
                type: "DELETE",
                data: {
                    ids: all_ids,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res){
                    all_ids.forEach(id => {
                        $('#employee_ids'+id).remove();
                    });
                    alert(res.success);
                    location.reload();
                },
                error: function(xhr){
                    alert('Terjadi kesalahan saat menghapus data');
                }
            });
        });
    });
});
</script>

<style>
/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }

    #printArea, #printArea * {
        visibility: visible;
    }

    #printArea button,
    #printArea .btn,
    #printArea .accordion-button {
        display: none !important;
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .d-xl-none {
        display: none !important;
    }

    .d-none.d-xl-block {
        display: block !important;
    }
}

/* Enhanced Styles */
.search-box .input-group-text {
    border-radius: 0.375rem 0 0 0.375rem;
}

.search-box .form-control {
    border-radius: 0 0.375rem 0.375rem 0;
}

.search-box .form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 0.5rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn {
    transition: all 0.2s;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Mobile Card Styles */
.accordion-button:not(.collapsed) {
    background-color: transparent;
    color: inherit;
}

.accordion-button:focus {
    box-shadow: none;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Table responsive improvements */
.table-responsive {
    -webkit-overflow-scrolling: touch;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>

{{-- MODAL CREATE & IMPORT --}}
@include('cash_bank.modal.create')
@include('cash_bank.modal.importExcel')

@endsection