<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Notifikasi Success/Error -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validasi Gagal!</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<div class="modal fade" id="ModalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data <span style="color:#FF7518">Bank Keluar</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bank-keluar.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Agenda</label>
                        <select name="agenda_tahun" id="dokumen_id" class="form-select" style="width:100%">
                            <option value="">Pilih Agenda atau ketik baru</option>
                            @foreach($agenda as $a)
                                <option value="{{ $a->dokumen_id }}" data-uraian="{{ $a->uraian }}" ...>{{ $a->agenda_tahun }}</option>
                            @endforeach
                        </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Sumber Dana</label>
                            <select name="id_sumber_dana" id="id_sumber_dana" class="form-select">
                                <option disabled selected>Pilih Sumber Dana</option>
                                @foreach($sumberDana as $sd)
                                    <option value="{{ $sd->id_sumber_dana }}">{{ $sd->nama_sumber_dana }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Bank Tujuan</label>
                            <!-- <select name="id_bank_tujuan" id="id_bank_tujuan" class="form-select">
                                <option disabled selected>Pilih Bank Tujuan</option>
                            </select> -->
                            <select name="id_bank_tujuan" id="id_bank_tujuan" class="form-select">
                                <option disabled selected>Pilih Bank Tujuan</option>
                                @foreach($bankTujuan as $bt)
                                    <option value="{{ $bt->id_bank_tujuan }}">{{ $bt->nama_tujuan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kriteria CF</label>
                            <!-- <select name="id_kategori_kriteria" id="kategori" class="form-select">
                                <option disabled selected>Pilih Kriteria CF</option>
                            </select> -->
                            <select name="id_kategori_kriteria" id="kategori" class="form-select">
                                <option disabled selected>Pilih Kriteria CF</option>
                                @foreach($kategoriKriteria as $kk)
                                    <option value="{{ $kk->id_kategori_kriteria }}">{{ $kk->nama_kriteria }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Sub Kriteria</label>
                            <select name="id_sub_kriteria" id="sub_kriteria" class="form-select">
                                <option disabled selected>Pilih Sub Kriteria</option>
                                @foreach($subKriteria as $sk)
                                    <option value="{{ $sk->id_sub_kriteria }}">{{ $sk->nama_sub_kriteria }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Item Sub Kriteria</label>
                            <select name="id_item_sub_kriteria" id="item_sub_kriteria" class="form-select">
                                 <option disabled selected>Pilih Item Sub Kriteria</option>
                                @foreach($itemSubKriteria as $ist)
                                    <option value="{{ $ist->id_item_sub_kriteria }}">{{ $ist->nama_item_sub_kriteria }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Uraian</label>
                        <textarea name="uraian" id="uraian" class="form-control" placeholder="Uraian"></textarea>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Penerima</label>
                            <input type="text" name="penerima" id="penerima" class="form-control" placeholder="Penerima">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pembayaran</label>
                            <input type="text" name="pembayaran" id="pembayaran" class="form-control" placeholder="pembayaran">
                        </div>

                        
                        
                    </div>
                    <div class="row mt-2"> 
                        <div class="col-md-4">
                            <label class="form-label">Kredit <span class="text-danger">*</span></label>
                            <input type="number" name="kredit" id="kredit" class="form-control" placeholder="0" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Nilai Ajuan</label>
                            <input type="number" name="nilai_rupiah" id="nilai_rupiah" class="form-control" placeholder="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {

    // format rupiah
    document.querySelectorAll('.rupiah').forEach(function(input){
        input.addEventListener('keyup', function(){
            let angka = this.value.replace(/[^0-9]/g, '');
            this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });
    // Inisialisasi Select2 saat modal dibuka
    $('#ModalCreate').on('shown.bs.modal', function () {
        console.log(' Modal opened');
        
        if (!$('#dokumen_id').hasClass('select2-hidden-accessible')) {
            $('#dokumen_id').select2({
                 tags: true, 
                dropdownParent: $('#ModalCreate'),
                placeholder: 'Pilih Agenda',
                allowClear: true
            });
            console.log('Select2 initialized');
        }
    });

    // Destroy Select2 saat modal ditutup (cleanup)
    $('#ModalCreate').on('hidden.bs.modal', function () {
        if ($('#dokumen_id').hasClass('select2-hidden-accessible')) {
            $('#dokumen_id').select2('destroy');
        }
        // Clear form
        $('#uraian').val('');
        $('#nilai_rupiah').val('');
        $('#penerima').val('');
        $('#pembayaran').val('');
    });

    // Event handler menggunakan event delegation dan change event
    $(document).on('change', '#dokumen_id', function() {
        const dokumenId = $(this).val();
        
        console.log('Agenda selected:', dokumenId);
        
        if(dokumenId && dokumenId !== '') {
            console.log('Fetching data...');
            
            // Tampilkan loading
            $('#uraian').val('Memuat data...');
            $('#nilai_rupiah').val('');
            $('#penerima').val('Memuat data...');
            $('#pembayaran').val('Memuat data...');
            
            $.ajax({
                url: '/get-dokumen-detail/' + dokumenId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(' Data received:', response);
                    
                    if(response.success && response.data) {
                        $('#uraian').val(response.data.uraian || '');
                        $('#nilai_rupiah').val(response.data.nilai_rupiah || '');
                        $('#penerima').val(response.data.penerima || '');
                        $('#pembayaran').val(response.data.pembayaran || '');
                        
                        console.log(' Form filled successfully!');
                    } else {
                        console.error(' Invalid response format');
                        alert('Data tidak ditemukan');
                        clearForm();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(' AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    alert('Gagal memuat data: ' + error);
                    clearForm();
                }
            });
        } else {
            clearForm();
        }
    });

    const bankMap = {
        "81029155533": "81029155533 - PPPBB",
        "81029155531": "81029155531 - UGKB",
        "81029155528": "81029155528 - GUNME",
        "81029155527": "81029155527 - PAGUN",
        "81029155526": "81029155526 - GUMAS",
        "81029155525": "81029155525 - RIMBA",
        "81029155524": "81029155524 - PARBA",
        "81029155523": "81029155523 - SINTANG",
        "81029155522": "81029155522 - NGABANG",
        "81029155521": "81029155521 - PANGA",
        "81029155520": "81029155520 - PARINDU",
        "81029155519": "81029155519 - PAPAR",
        "81029155518": "81029155518 - BAYAN",
        "81029155517": "81029155517 - PAKEM",
        "81029155530": "81029155530 - UGKST",
        "81029155516": "81029155516 - DASAL",
        "81029155515": "81029155515 - TAMBA",
        "81029155514": "81029155514 - PAMUKAN",
        "81029155513": "81029155513 - PAPAM",
        "81029155512": "81029155512 - BALIN",
        "81029155511": "81029155511 - PELAIHARI",
        "81029155510": "81029155510 - PALAI",
        "81029155509": "81029155509 - KUMAI",
        "81029155532": "81029155532 - PRYBB",
        "81029155529": "81029155529 - UGKT",
        "81029155508": "81029155508 - TABARA",
        "81029155507": "81029155507 - TAJATI",
        "81029155506": "81029155506 - PANDAWA",
        "81029155505": "81029155505 - PALPI",
        "81029155504": "81029155504 - PASAM",
        "81029155503": "81029155503 - LONGKALI",
        "81029155502": "81029155502 - DEKAN",
        "81029155501": "81029155501 - RAREN"
    };

    $('#uraian').on('keyup change', function () {
        let uraian = $(this).val().trim();
        if (!uraian) return;

        // Ambil kata pertama sebelum |
        let kode = uraian.split('|')[0];

        if (bankMap[kode]) {
            let namaBank = bankMap[kode];

            // Cari option bank tujuan yang sesuai
            $('#id_bank_tujuan option').each(function () {
                if ($(this).text().includes(namaBank)) {
                    $('#id_bank_tujuan').val($(this).val()).trigger('change');
                }
            });
        }
    });


    function clearForm() {
        $('#uraian').val('');
        $('#nilai_rupiah').val('');
        $('#penerima').val('');
        $('#pembayaran').val('');
    }

    // Sub Kriteria
    // $('#kategori').on('change', function () {
    //     let id = $(this).val();
    //     $.get('/get-sub-kriteria/' + id, function (res) {
    //         $('#sub_kriteria').empty().append('<option disabled selected>Pilih Sub Kriteria</option>');
    //         res.forEach(e => {
    //             $('#sub_kriteria').append(`<option value="${e.id_sub_kriteria}">${e.nama_sub_kriteria}</option>`);
    //         });
    //         $('#item_sub_kriteria').empty().append('<option disabled selected>Pilih Item Sub Kriteria</option>');
    //     });
    // });
    // Sub Kriteria
    $('#kategori').on('change', function () {
        let id = $(this).val();

        $.get('/get-sub-kriteria/' + id, function (res) {
            $('#sub_kriteria').empty().append('<option disabled selected>Pilih Sub Kriteria</option>');
            res.forEach(e => {
                $('#sub_kriteria').append(`<option value="${e.id_sub_kriteria}">${e.nama_sub_kriteria}</option>`);
            });

            $('#item_sub_kriteria').empty().append('<option disabled selected>Pilih Item Sub Kriteria</option>');
        });
    });

    // Item Sub Kriteria
    $('#sub_kriteria').on('change', function () {
        let id = $(this).val();

        $.get('/get-item-sub-kriteria/' + id, function (res) {
            $('#item_sub_kriteria').empty().append('<option selected disabled>Pilih Item Sub Kriteria</option>');

            res.forEach(e => {
                $('#item_sub_kriteria').append(
                    `<option value="${e.id_item_sub_kriteria}">${e.nama_item_sub_kriteria}</option>`
                );
            });
        });
    });


     // Prevent double submit
    $('#formBankKeluar').on('submit', function(e) {
        console.log('📤 Form submitting...');
        
        const btn = $('#btnSubmit');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
        
        // Re-enable after 3 seconds as fallback
        setTimeout(function() {
            btn.prop('disabled', false).html('<i class="bi bi-save"></i> Simpan');
        }, 3000);
    });
});
</script>