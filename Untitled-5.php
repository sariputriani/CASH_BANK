<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Saldo VA </title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" >
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" >
  <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}" >
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <!-- /.navbar -->

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Data Saldo VA <span style="color: #FF7518">{{ $data->first()->nama_tujuan ?? '-' }}</span></h1>
          </div>
        </div>
        <div class="row mb-2 justify-content-between">
          <div class="col-md-3">
            <select name="tahun" class="form-select" onchange="submitForm()">
              <label class="form-label">Tahun</label>

            </select>
          </div>
          <div class="col-md-3">
             
          <div>
            <a href="#" onclick="window.print()" class="btn-export m-3 bg-success" style="border-radius: 10px; padding:15px;color: white;">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>   
            <a href="#" onclick="window.print()" class="bg-danger" style="border-radius: 10px; padding:15px;color: white;">
                <i class="bi bi-printer"></i> Export PDF
            </a>
          </div>

        </div>

      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <!-- <div class="card-header">
                <h3 class="card-title">DataTable with default features</h3>
              </div> -->
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Agenda</th>
                    <th>No Bukti</th>
                    <th>Bank Tujuan</th>
                    <th>Penerima</th>
                    <th>Debet</th>
                    <th>Kredit</th>
                    <th>Saldo Akhir</th>
                    <th>No SAP</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  @forelse($data as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>

                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $row->agenda ?? '-' }}</td>
                        <td>{{ $row->no_bukti ?? '-' }}</td>
                        <td>{{ $row->nama_tujuan }}</td>
                        <td>{{ $row->penerima ?? '-' }}</td>

                        <td class="text-end">@currency($row->debet)</td>
                        <td class="text-end">@currency($row->kredit)</td>
                        <td class="text-end fw-bold">@currency($row->saldo_akhir)</td>

                        <td class="text-center">
                            {{ $row->no_sap ?? '-' }}
                        </td>

                        {{-- AKSI (HANYA UNTUK BANK KELUAR) --}}
                        <td class="text-center">
                            @if($row->id_bank_keluar)
                            <button
                                class="btn btn-sm btn-primary btn-edit-sap"
                                data-id="{{ $row->id_bank_keluar }}"
                                data-no_sap="{{ $row->no_sap }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editSAP">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            @else
                                -
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">
                            Tidak ada data
                        </td>
                    </tr>
                    @endforelse
                  </tbody>

                  <tfoot>
                    <tr>
                      <td colspan=8 class="text-center"><b>Saldo Akhir </b></td>
                      <td><b>@currency($row->saldo_akhir)</b></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
  <!-- <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 3.2.0-rc
    </div>
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer> -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js')}}"></script>
<!-- Page specific script -->