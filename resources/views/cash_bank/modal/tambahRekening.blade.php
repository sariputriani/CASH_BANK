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
<div class="modal fade" id="ModalTambahRekening" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data <span style="color:#FF7518">Rekening Bank</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{route('daftarRekening.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Nama Bank</label>
                           <select name="id_daftar_bank" id="namaBank" class="form-select">
                                @foreach($daftarBank as $nb)
                                    <option value="{{ $nb->id_daftar_bank }}">{{ $nb->nama_bank }}</option>
                                @endforeach
                            </select>
                            <!-- <input type="text" name="namBank" id="namBank" class="form-control border-bottom-0" placeholder="BCA" step="0.01"> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" id="nomor_rekening" class="form-control border-bottom-0" placeholder="BCA" step="0.01">
                        </div>
                    </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>
