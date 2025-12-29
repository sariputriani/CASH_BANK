@extends("layouts/index")
@section('content')

<div class="container-fluid mt-4">
    <h1 class="tittle">Report <span style="color: #FF7518">Bank Masuk</span></h1>
    <small>Ini daftar Report Bank Masuk</small>
   
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    
                    <form action="{{ route('bank-masuk.report') }}" method="GET" id="filterForm">
                        <div class="row g-3 align-items-end">

                            <!-- Tahun -->
                            <div class="col-md-1">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select" onchange="submitForm()">
                                    @foreach($tahunList as $t)
                                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                            {{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jenis Pembayaran -->
                            <div class="col-md-2">
                                <label class="form-label">Jenis Pembayaran</label>
                                <select name="id_jenis_pembayaran" class="form-select" onchange="submitForm()">
                                    <option value="">Semua Jenis Pembayaran</option>
                                    @foreach($jenisPembayaranList as $jp)
                                        <option value="{{ $jp->id_jenis_pembayaran }}"
                                            {{ request('id_jenis_pembayaran') == $jp->id_jenis_pembayaran ? 'selected' : '' }}>
                                            {{ $jp->nama_jenis_pembayaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Kategori (MULTI) -->
                            <div class="col-md-3">
                                <label class="form-label">Kategori</label>
                                <div class="dropdown">
                                    <button class="form-select text-start" type="button" data-bs-toggle="dropdown">
                                        <span id="kategoriText">Semua Kategori</span>
                                    </button>

                                    <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside">
                                        <div class="form-check fw-bold">
                                            <input class="form-check-input" type="checkbox" id="kategoriAll">
                                            <label class="form-check-label">Pilih Semua</label>
                                        </div>
                                        <hr>

                                        @foreach($kategoriList as $k)
                                            <div class="form-check">
                                                <input class="form-check-input kategori-item"
                                                    type="checkbox"
                                                    name="kategori[]"
                                                    value="{{ $k->id_kategori_kriteria }}"
                                                    {{ in_array($k->id_kategori_kriteria, request('kategori', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $k->nama_kriteria }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Sumber Dana (MULTI) -->
                            <div class="col-md-3">
                                <label class="form-label">Sumber Dana</label>
                                <div class="dropdown">
                                    <button class="form-select text-start" type="button" data-bs-toggle="dropdown">
                                        <span id="sdText">Semua Sumber Dana</span>
                                    </button>

                                    <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside">
                                        <div class="form-check fw-bold">
                                            <input class="form-check-input" type="checkbox" id="sdAll">
                                            <label class="form-check-label">Pilih Semua</label>
                                        </div>
                                        <hr>

                                        @foreach($sumberDanaList as $sd)
                                            <div class="form-check">
                                                <input class="form-check-input sd-item"
                                                    type="checkbox"
                                                    name="sumber_dana[]"
                                                    value="{{ $sd->id_sumber_dana }}"
                                                    {{ in_array($sd->id_sumber_dana, request('sumber_dana', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $sd->nama_sumber_dana }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Tujuan -->
                            <div class="col-md-3">
                                <label class="form-label">Bank Tujuan</label>
                                <select name="bankTujuan" class="form-select" onchange="submitForm()">
                                    <option value="">Semua Bank Tujuan</option>
                                    @foreach($bankTujuanList as $b)
                                        <option value="{{ $b->id_bank_tujuan }}"
                                            {{ request('bankTujuan') == $b->id_bank_tujuan ? 'selected' : '' }}>
                                            {{ $b->nama_tujuan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm" id="printArea">
                <div class="card-body table-responsive">
                    <div class="row">
                        <div class="col-md-12  d-flex justify-content-between mb-4 align-content-center">
                            <h5 class="tittle">Daftar Bank Masuk</h5>
                            <div class="">

                                <a href="#" onclick="window.print()" class="btn-export m-3" style="border-radius: 10px; padding:15px;color: white;background-color: blue">
                                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                                </a>   
                                <a href="#" onclick="window.print()" class="btn-export " style="border-radius: 10px; padding:15px;color: white;background-color: green">
                                    <i class="bi bi-printer"></i> Export PDF
                                </a>   
                                <button type="button" class="btn btn-sm bg-primary btn-outline-secondary text-white" onclick="resetAllFilters()" style="margin:10px;">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button> 
                            </div>

                        </div>
                    </div>

                    @if($data->count())
                        <p class="text-muted">
                            Menampilkan data berdasarkan filter yang dipilih
                        </p>

                        <table class="table table-bordered table-striped mt-3">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Agenda Tahun</th>
                                    <th>Uraian</th>
                                    <th>Penerima</th>
                                    <th>Sumber Dana</th>
                                    <th>Bank Tujuan</th>
                                    <th>Kategori</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Debet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $a)
                                <tr>
                                    <td>{{ $a->tanggal }}</td>
                                    <td>{{ $a->agenda_tahun }}</td>
                                    <td>{{ $a->uraian }}</td>
                                    <td>{{ $a->penerima }}</td>
                                    <td>{{ $a->sumberDana->nama_sumber_dana ?? '-' }}</td>
                                    <td>{{ $a->bankTujuan->nama_tujuan ?? '-' }}</td>
                                    <td>{{ $a->kategori->nama_kriteria ?? '-' }}</td>
                                    <td>{{ $a->jenisPembayaran->nama_jenis_pembayaran ?? '-' }}</td>
                                    <td class="text-end">@currency($a->debet)</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-end">TOTAL</th>
                                    <th class="text-end">
                                        {{ number_format($data->sum('debet'),0,',','.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p class="text-center mt-3">Data tidak ditemukan</p>
                    @endif

                  

                    

                    <!-- @if(request('kategori') && is_array(request('kategori')) && count(request('kategori')) > 0)
                        
                        <h5 class="mt-4">Rekapan Kategori</h5>
                       
                        <table class="table table-bordered table-striped mt-2" >
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Total Saldo (Kredit)</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($kategoriList as $i => $row)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $row->nama_kriteria }}</td>
                                        <td class="text-end"> {{ number_format($data->sum('debet'), 0, ',', '.') }}</td> 
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif -->

                    <hr>
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

     #printArea .btn-export{
            background-color: #0d6efd;
    color: #ffffff;
    }

}
</style>

<script>
function submitForm() {
    document.getElementById('filterForm').submit();
}

document.addEventListener('DOMContentLoaded', () => {

    function setupMulti(allId, itemClass, textId, defaultText) {
        const all = document.getElementById(allId);
        const items = document.querySelectorAll(itemClass);
        const text = document.getElementById(textId);

        function updateText() {
            const checked = document.querySelectorAll(itemClass + ':checked').length;
            text.textContent = checked ? checked + ' dipilih' : defaultText;
        }

        all.addEventListener('change', () => {
            items.forEach(i => i.checked = all.checked);
            updateText();
            submitForm();
        });

        items.forEach(i => i.addEventListener('change', () => {
            updateText();
            submitForm();
        }));

        updateText();
    }

    setupMulti('sdAll', '.sd-item', 'sdText', 'Semua Sumber Dana');
    setupMulti('kategoriAll', '.kategori-item', 'kategoriText', 'Semua Kategori');
});
function resetAllFilters() {
    const form = document.getElementById('filterForm');
    const tahun = form.querySelector('[name="tahun"]')?.value;
    window.location.href = form.action + '?tahun=' + tahun;
}
</script>
@endsection