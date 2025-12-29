<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        
        <!-- style css -->
        <link rel="stylesheet"  href="{{ asset('css/style.css') }}" class="css">
       <link rel="stylesheet" href="https://unpkg.com/virtual-select-plugin@1.0.37/dist/virtual-select.min.css">
        <script src="https://unpkg.com/virtual-select-plugin@1.0.37/dist/virtual-select.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <title>Document</title>

        <link rel="stylesheet" href="https://unpkg.com/virtual-select-plugin@1.0.37/dist/virtual-select.min.css">
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center py-5">
      <div class="row w-100 justify-content-center">
          <div class="col-lg-11 col-xl-10">
              <div class="card border-0 rounded-2 bg-white">
                  <div class="card-body p-4">
                        <!-- <div class="text-center gap-4 d-flex">
                            <div class="text-start mb-4">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn border-0">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="text-center gap-4 mb-4">
                                <h3 class="fw-bold text-dark">
                                    Data Saldo VA
                                    <span style="color:#FF7518">
                                        {{ $data->first()->nama_tujuan ?? '-' }}
                                    </span>
                                </h3>
                                <p class="text-muted small mb-0">Laporan Transaksi Keuangan</p>
                            </div>
                        </div> -->
                        <div class="d-flex align-items-center mb-4">

                            <!-- LOGOUT -->
                            <form action="{{ route('logout') }}" method="POST" class="me-3">
                                @csrf
                                <button type="submit" class="btn border-0 bg-primary text-white" style="background-color:#FF7518">
                                    <i class="bi bi-box-arrow-left fs-5"></i>
                                </button>
                            </form>

                            <!-- JUDUL (CENTER) -->
                            <div class="flex-grow-1 text-center">
                                <h3 class="fw-bold text-dark mb-1">
                                    Data Saldo VA
                                    <span style="color:#FF7518">
                                        {{ $data->first()->nama_tujuan ?? '-' }}
                                    </span>
                                </h3>
                                <p class="text-muted small mb-0">
                                    Laporan Transaksi Keuangan
                                </p>
                            </div>

                        </div>


                      <hr class="mb-4">
                      <div class="row mt-3 mb-4">
                        <div class="col-md-12 d-flex justify-content-between mt-3">
                         
                          <div class="col-md-4">
                                  <!-- <label class="form-label">Export PDF</label> -->
                                    <!-- <a href="#" onclick="window.print()" class="btn-export  m-1 p-3 bg-success" style="border-radius: 10px; color: white;">
                                     <i class="bi bi-file-spreadsheet-fill"></i></i> Export Excel
                                  </a>    -->
                                  <a href="{{ url('/user-sap/export_excel')}}" class="btn btn-outline-success"><i class="bi bi-printer"></i>
                                        Download Excel
                                    </a>
                                       <a href="{{ url('/user-sap/view/pdf')}}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer"></i>Download PDF</a>
                                  <!-- <a href="#" onclick="window.print()" class="btn-export m-1 p-3 bg-danger" style="border-radius: 10px; color: white;">
                                      <i class="bi bi-file-pdf-fill"></i> Export PDF
                                  </a>    -->
                                  
                          </div>
                           <div class="col-md-1">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select" onchange="submitForm()">
                                   @foreach($tahun as $t)
                                   <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                       {{ $t }}
                                   </option>
                                   @endforeach
                                </select>
                          </div>
                        </div>
                      </div>
                     
                      <div class="table-responsive">
                          @include('cash_bank.exportExcel.userSap', ['data' => $data])
                          @include('cash_bank.modal.editSap')
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

    #printArea .form-select {
        display: none !important;
        visibility: hidden !important;
    }

     #printArea .aksi,
    #printArea th:last-child {
        display: none !important;
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}


</style>
<script>
$(document).on('click', '.btn-edit-sap', function () {
    $('#id_bank_keluar').val($(this).data('id'));
    $('#no_sap').val($(this).data('no_sap'));
});

$('#formSAP').on('submit', function(e){
    e.preventDefault();

    let id = $('#id_bank_keluar').val();

    $.ajax({
        url: '/user-sap/' + id,
        type: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            no_sap: $('#no_sap').val()
        },
        success: function(res) {
            alert(res.message);
            location.reload();
        },
        error: function(xhr) {
            alert(xhr.responseText);
        }
    });
});
  </script>
</body>
</html>

