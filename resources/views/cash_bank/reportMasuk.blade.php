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

                                <!-- <a href="#" onclick="window.print()" class="btn-export m-3" style="border-radius: 10px; padding:15px;color: white;background-color: blue">
                                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                                </a>    -->
                                <!-- <a href="{{ url('/bank-masuk/report_export_excel')}}" class="btn bg-primary">Download Excel</a> -->
                                 <a href="{{ route('bank-masuk.report_export_excel', [
                                        'tahun' => request('tahun'),
                                        'id_jenis_pembayaran' => request('id_jenis_pembayaran'),
                                        'kategori' => request('kategori'),
                                        'sumber_dana' => request('sumber_dana'),
                                        'bankTujuan' => request('bankTujuan'),
                                        
                                        
                                    ]) }}" class="btn  btn-outline-primary"><i class="bi bi-file-earmark-spreadsheet"></i>
                                        Download Excel
                                    </a>
                                <!-- <a href="#" onclick="window.print()" class="btn-export " style="border-radius: 10px; padding:15px;color: white;background-color: green">
                                    <i class="bi bi-printer"></i> Export PDF
                                </a>    -->
                                 <a href="{{ route('bank-masuk.reportMasukPdf', [
                                        'tahun' => request('tahun'),
                                        'id_jenis_pembayaran' => request('id_jenis_pembayaran'),
                                        'kategori' => request('kategori'),
                                        'sumber_dana' => request('sumber_dana'),
                                        'bankTujuan' => request('bankTujuan'),



                                    ]) }}" target="_blank" class="btn btn-outline-danger"><i class="bi bi-printer"></i>
                                        Download PDF
                                    </a>
                                <button type="button" class="btn btn-sm bg-primary btn-outline-secondary text-white" onclick="resetAllFilters()" style="margin:10px;">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button> 
                            </div>

                        </div>
                    </div>

                    @include('cash_bank.exportExcel.excelReportMasuk', ['data' => $data])
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