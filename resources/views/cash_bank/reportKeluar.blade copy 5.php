@extends("layouts/index")
@section('content')

<div class="container-fluid mt-4">
    <h1 class="tittle">Report <span style="color: #FF7518">Bank Keluar</span></h1>
    <small>Ini daftar Report Bank Keluar</small>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('bank-keluar.report') }}" method="GET" id="filterForm">
                        <div class="row g-3 align-items-end">

                            <!-- Tahun -->
                            <div class="col-md-3">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select" onchange="submitForm()">
                                    @foreach($tahunList as $t)
                                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                            {{ $t }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Bulan -->
                            <div class="col-md-3">
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select" onchange="submitForm()">
                                    <option value="">Semua Jenis Bulan</option>
                                    @foreach($bulanList as $b)
                                        <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($b)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal (Multi-select Dropdown) -->
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <div class="dropdown">
                                    <button class="form-select text-start" type="button" data-bs-toggle="dropdown">
                                        <span id="tanggalText">-- Pilih Tanggal --</span>
                                    </button>

                                    <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside" style="max-height: 300px; overflow-y: auto;">
                                        <div class="form-check fw-bold">
                                            <input class="form-check-input" type="checkbox" id="tanggalAll">
                                            <label class="form-check-label">Pilih Semua</label>
                                        </div>
                                        <hr class="my-1">

                                        @foreach($tanggalList as $tt)
                                        <div class="form-check">
                                            <input class="form-check-input tanggal-item"
                                                type="checkbox"
                                                name="tanggal[]"
                                                value="{{ $tt }}"
                                                {{ in_array($tt, request('tanggal', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                {{ \Carbon\Carbon::parse($tt)->format('d-m-Y') }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Tujuan -->
                            <div class="col-md-3">
                                <label class="form-label">Bank Tujuan</label>
                                <select name="bank_tujuan" class="form-select" onchange="submitForm()">
                                    <option value="" disable>Semua Bank Tujuan</option>
                                    @foreach($bankTujuanList as $bank)
                                        <option value="{{ $bank->id_bank_tujuan }}" {{ request('bank_tujuan') == $bank->id_bank_tujuan ? 'selected' : '' }}>
                                            {{ $bank->nama_tujuan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sumber Dana -->
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
                                        <hr class="my-1">

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

                            <!-- Kategori -->
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
                                        <hr class="my-1">

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

                            <!-- Jenis Pembayaran -->
                            <div class="col-md-3">
                                <label class="form-label">Jenis Pembayaran</label>
                                <select name="id_jenis_pembayaran" class="form-select" onchange="submitForm()">
                                    <option value="">Semua Jenis Pembayaran</option>
                                    @foreach($jenisPembayaranList as $rjs)
                                        @if($rjs->id_jenis_pembayaran)
                                            <option value="{{ $rjs->id_jenis_pembayaran }}"
                                                {{ request('id_jenis_pembayaran') == $rjs->id_jenis_pembayaran ? 'selected' : '' }}>
                                                {{ $rjs->nama_jenis_pembayaran }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rekapan -->
                            <div class="col-md-3">
                                <label class="form-label">Rekapan</label>
                                <select name="rekapanVA" class="form-select" onchange="submitForm()">
                                    <option value="">-- Pilih Rekapan --</option>
                                    <option value="va" {{ request('rekapanVA') == 'va' ? 'selected' : '' }}>Saldo Bank</option>
                                    <option value="bank" {{ request('rekapanVA') == 'bank' ? 'selected' : '' }}>Saldo VA</option>
                                    <option value="kategori-full" {{ request('rekapanVA') == 'kategori-full' ? 'selected' : '' }}>Kategori Full</option>
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
                        <div class="col-md-12 d-flex justify-content-between mb-4 align-content-center gap-2">
                            <h5 class="tittle">Daftar Bank Keluar</h5>
                            <div>
                                <a href="#" onclick="window.print()" class="btn-export m-3 bg-success" style="border-radius: 10px; padding:15px;color: white;">
                                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                                </a>   
                                <a href="#" onclick="window.print()" class="btn-export bg-danger" style="border-radius: 10px; padding:15px;color: white;">
                                    <i class="bi bi-printer"></i> Export PDF
                                </a>
                                <button type="button" class="btn btn-sm bg-primary btn-outline-secondary text-white" onclick="resetAllFilters()" style="margin:10px;">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>   
                            </div>
                        </div>
                    </div>

                    {{-- INFO FILTER AKTIF --}}
                    @php
                        $filterInfo = [];
                        if(request('tahun')) $filterInfo[] = 'Tahun: '.request('tahun');
                       
                        if (request('bulan')) {
                            $bulan = (int) request('bulan');
                            if ($bulan >= 1 && $bulan <= 12) {
                                $filterInfo[] = 'Bulan: ' . \Carbon\Carbon::create()->month($bulan)->format('F');
                            }
                        }
                        if(request('tanggal') && count(request('tanggal')) > 0) $filterInfo[] = count(request('tanggal')).' Tanggal';
                        if(request('bank_tujuan')) {
                            $bankName = $bankTujuanList->where('id_bank_tujuan', request('bank_tujuan'))->first()->nama_tujuan ?? '';
                            $filterInfo[] = 'Bank: '.$bankName;
                        }
                        if(request('sumber_dana') && count(request('sumber_dana')) > 0) {
                            $sdNames = $sumberDanaList->whereIn('id_sumber_dana', request('sumber_dana'))->pluck('nama_sumber_dana')->take(2)->toArray();
                            $filterInfo[] = 'SD: '.implode(', ', $sdNames).(count(request('sumber_dana')) > 2 ? ' +lainnya' : '');
                        }
                        if(request('kategori') && count(request('kategori')) > 0) {
                            $filterInfo[] = count(request('kategori')).' Kategori';
                        }
                        if(request('id_jenis_pembayaran')) {
                            $jpName = $jenisPembayaranList->where('id_jenis_pembayaran', request('id_jenis_pembayaran'))->first()->nama_jenis_pembayaran ?? '';
                            $filterInfo[] = 'JP: '.$jpName;
                        }
                    @endphp

                    <!-- {{-- ALERT MODE TAMPILAN --}}
                    <div class="alert {{ $showDebet ? 'alert-success' : 'alert-warning' }} mt-3">
                        <strong>📊 Mode Tampilan:</strong>
                        @if($showDebet && $showSaldoAkhir && $showSAP)
                            <span class="badge bg-success">Lengkap (Debet + Kredit + Saldo + SAP)</span>
                            <small class="d-block mt-1">Filter aktif: {{ $countActiveFilters }} (menampilkan semua kolom)</small>
                        @elseif($showDebet && $showSaldoAkhir)
                            <span class="badge bg-info">Debet + Kredit + Saldo</span>
                            <small class="d-block mt-1">Filter aktif: {{ $countActiveFilters }} (tanpa SAP)</small>
                        @else
                            <span class="badge bg-warning text-dark">Kredit Saja</span>
                            <small class="d-block mt-1">Filter aktif: {{ $countActiveFilters }} (tampilan spesifik)</small>
                        @endif
                    </div> -->

                    {{-- REKAP SALDO BANK --}}
                    @if(request('rekapanVA') === 'bank')
                        <div class="alert alert-info mt-3">
                            <strong>📊 Rekapan Saldo Virtual Account (VA)</strong><br>
                            <small>
                                @if(count($filterInfo) > 0)
                                    Filter: {{ implode(' | ', $filterInfo) }}
                                @else
                                    Menampilkan semua data
                                @endif
                            </small>
                        </div>

                        <table class="table table-bordered table-striped mt-2">
                            <thead class="text-center table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Akun VA</th>
                                    <!-- @if($showDebet)
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                    @else
                                        <th>Kredit</th>
                                    @endif -->
                                    <th>Saldo VA</th>
                                    @if($showSAP)
                                        <th>Saldo SAP</th>
                                        <th>Selisih</th>
                                    @endif
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapVA as $i => $row)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $row['bank'] ?? '-' }}</td>
                                        <!-- @if($showDebet)
                                            <td class="text-end">@currency($row['debet'] ?? 0)</td>
                                        @endif -->
                                        <!-- <td class="text-end">@currency($row['kredit'] ?? 0)</td> -->
                                        <td class="text-end fw-bold">@currency($row['saldo_va'] ?? 0)</td>
                                        @if($showSAP)
                                            <td class="text-end">@currency($row['saldo_sap'] ?? 0)</td>
                                            <td class="text-end fw-bold">@currency($row['selisih'] ?? 0)</td>
                                        @endif
                                        <td>{{ $row['keterangan'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $showDebet && $showSAP ? 8 : ($showDebet ? 6 : ($showSAP ? 6 : 4)) }}" class="text-center text-muted py-3">
                                            Tidak ada data dengan filter yang dipilih
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($rekapVA) > 0)
                                <tfoot class="table-secondary">
                                    <tr>
                                        <!-- <th colspan="{{ $showDebet ? 3 : 2 }}" class="text-end">TOTAL:</th> -->
                                        <th colspan="2" class="text-end">TOTAL:</th>
                                        <th class="text-end">@currency(collect($rekapVA)->sum('saldo_va'))</th>
                                        <th colspan="{{ $showSAP ? 3 : 1 }}"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    @endif

                    {{-- REKAP SALDO VA --}}
                    @if(request('rekapanVA') === 'va')
                        <div class="alert alert-info mt-3">
                            <strong>📊 Rekapan Saldo Bank</strong><br>
                            <small>
                                @if(count($filterInfo) > 0)
                                    Filter: {{ implode(' | ', $filterInfo) }}
                                @else
                                    Menampilkan semua data
                                @endif
                            </small>
                        </div>

                        <table class="table table-bordered table-striped mt-2">
                            <thead class="text-center table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Akun Bank</th>
                                    <!-- @if($showDebet)
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                    @else -->
                                        <!-- <th>Kredit</th>
                                    @endif -->
                                    <th>Saldo Bank</th>
                                    <!-- @if($showSAP)
                                        <th>Saldo SAP</th>
                                        <th>Selisih</th>
                                    @endif -->
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapVA as $i => $row)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $row['bank'] ?? '-' }}</td>
                                        <!-- @if($showDebet)
                                            <td class="text-end">@currency($row['debet'] ?? 0)</td>
                                        @endif -->
                                        <!-- <td class="text-end">@currency($row['kredit'] ?? 0)</td> -->
                                        <td class="text-end fw-bold">@currency($row['saldo_va'] ?? 0)</td>
                                        <!-- @if($showSAP) -->
                                            <!-- <td class="text-end">@currency($row['saldo_sap'] ?? 0)</td> -->
                                            <!-- <td class="text-end">@currency($row['selisih'] ?? 0)</td> -->
                                        <!-- @endif -->
                                        <td>{{ $row['keterangan'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-3">
                                        <!-- <td colspan="{{ $showDebet && $showSAP ? 8 : ($showDebet ? 6 : ($showSAP ? 6 : 4)) }}" class="text-center text-muted py-3"> -->
                                            Tidak ada data dengan filter yang dipilih
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($rekapVA) > 0)
                                <tfoot class="table-secondary">
                                    <tr>
                                        <!-- <th colspan="{{ $showDebet ? 3 : 2 }}" class="text-end">TOTAL:</th> -->
                                        <th colspan="2" class="text-end">TOTAL:</th>
                                        <th class="text-end">@currency(collect($rekapVA)->sum('saldo_va'))</th>
                                        <th colspan="{{ $showSAP ? 3 : 1 }}"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    @endif

                    {{-- REKAP KATEGORI FULL --}}
                    @if(request('rekapanVA') === 'kategori-full')
                        <div class="alert alert-info mt-3">
                            <strong>📊 Rekapan Kategori Full</strong><br>
                            <small>
                                @if(count($filterInfo) > 0)
                                    Filter: {{ implode(' | ', $filterInfo) }}
                                @else
                                    Menampilkan semua data
                                @endif
                            </small>
                        </div>

                        <table class="table table-bordered table-striped mt-2" >
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Sub Kriteria</th>
                                    <th>Item</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapVA as $kategori => $subs)
                                    @php
                                        $rowspanKategori = 0;
                                        $totalKategori = 0;
                                        
                                        foreach ($subs as $sub => $items) {
                                            $rowspanKategori += count($items) + 1;
                                            foreach ($items as $item) {
                                                if (isset($item['kredit'])) {
                                                    $totalKategori += floatval($item['kredit']);
                                                }
                                            }
                                        }
                                        $rowspanKategori += 1;
                                        $firstKategori = false;
                                    @endphp

                                    @foreach($subs as $sub => $items)
                                        @php
                                            $rowspanSub = count($items) + 1;
                                            $totalSub = array_sum(array_column($items, 'kredit'));
                                            $firstSub = false;
                                        @endphp

                                        @foreach($items as $item)
                                            <tr>
                                                @if(!$firstKategori)
                                                    <td rowspan="{{ $rowspanKategori }}" class="kolom-kategori">{{ $kategori }}</td>
                                                    @php $firstKategori = true; @endphp
                                                @endif

                                                @if(!$firstSub)
                                                    <td rowspan="{{ $rowspanSub }}" class="kolom-kategori">{{ $sub }}</td>
                                                    @php $firstSub = true; @endphp
                                                @endif
                                                <td >{{ $item['item'] ?? '-' }}</td>
                                                 <td class="text-end">
                                                    <a href="{{ route('detailItem.index', [
                                                        'kategori' => $kategori,
                                                        'sub' => $sub,
                                                        'item' => $item['item'] ?? '-',
                                                        'tahun' => request('tahun'),
                                                        'bulan' => request('bulan'),
                                                        'tanggal' => request('tanggal'),
                                                        'bank_tujuan' => request('bank_tujuan'),
                                                        'sumber_dana' => request('sumber_dana'),
                                                        'id_jenis_pembayaran' => request('id_jenis_pembayaran'),
                                                        
                                                    ]) }}" 
                                                    class="text-decoration-none text-dark ">
                                                        @currency($item['kredit'] ?? 0)
                                                    </a>
                                                </td>
                                                
                                                <!-- <td class="text-end ">@currency($item['kredit'] ?? 0)</td> -->
                                            </tr>
                                        @endforeach

                                        <tr class="table-light">
                                            <td class="text-end fw-bold">TOTAL {{ $sub }}</td>
                                            <td class="text-end fw-bold">@currency($totalSub)</td>
                                        </tr>
                                    @endforeach

                                    <tr class="table-danger">
                                        <td class="text-end fw-bold" colspan="2">TOTAL {{ strtoupper($kategori) }}</td>
                                        <td class="text-end fw-bold">@currency($totalKategori)</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            Tidak ada data kategori dengan filter yang dipilih
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif

                    {{-- TABEL DATA NORMAL --}}
                    @if(!request('rekapanVA'))
                        <div class="alert alert-info mt-3">
                            <small>Filter: {{ implode(' | ', $filterInfo ?? ['Semua Data']) }}</small>
                        </div>

                        <table class="table table-bordered table-striped">
                            <thead class="text-center table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    
                                    {{-- Kolom Bank Tujuan - tampil jika difilter --}}
                                    @if($bankTujuanId || request('bank_tujuan') === '-- Pilih Bank Tujuan --')
                                        <th>Bank Tujuan</th>
                                    @endif
                                    
                                    {{-- Kolom Sumber Dana - tampil jika difilter --}}
                                    @if($sumberDanaIds && count($sumberDanaIds) > 0)
                                        <th>Sumber Dana</th>
                                    @endif
                                    
                                    {{-- Kolom Kategori - tampil jika difilter --}}
                                    @if($kategoriIds && count($kategoriIds) > 0)
                                        <th>Kategori</th>
                                    @endif
                                    
                                    {{-- Kolom Jenis Pembayaran - tampil jika difilter --}}
                                    @if($idJenisPembayaran)
                                        <th>Jenis Pembayaran</th>
                                    @endif
                                    
                                    <th>Penerima</th>
                                    <th>Bank Tujuan</th>
                                    <th>Sumber Dana</th>
                                    <th>Kategori</th>
                                    <th>Uraian</th>
                                    
                                    {{-- Kolom Debet - tampil di mode 0-1 filter --}}
                                    @if($showDebet)
                                        <th>Debet</th>
                                    @endif
                                    
                                    <th>Kredit</th>
                                    
                                    {{-- Kolom Saldo Akhir - tampil di mode 0-1 filter --}}
                                    @if($showSaldoAkhir)
                                        <th>Saldo Akhir</th>
                                    @endif
                                    
                                    {{-- Kolom No SAP --}}
                                    @if($showSAP)
                                        <th>No SAP</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $row)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                        
                                        {{-- Data Bank Tujuan --}}
                                        @if($bankTujuanId)
                                            <td>{{ $row->nama_tujuan ?? '-' }}</td>
                                        @endif
                                        
                                        {{-- Data Sumber Dana --}}
                                        @if($sumberDanaIds && count($sumberDanaIds) > 0)
                                            <td>{{ $row->nama_sumber_dana ?? '-' }}</td>
                                        @endif
                                        
                                        {{-- Data Kategori --}}
                                        @if($kategoriIds && count($kategoriIds) > 0)
                                            <td>{{ $row->nama_kriteria ?? '-' }}</td>
                                        @endif
                                        
                                        {{-- Data Jenis Pembayaran --}}
                                        @if($idJenisPembayaran)
                                            <td>{{ $row->nama_jenis_pembayaran ?? '-' }}</td>
                                        @endif
                                        
                                        <td>{{ $row->penerima ?? '-' }}</td>
                                        <td>{{ $row->nama_tujuan ?? '-' }}</td>
                                        <td>{{ $row->nama_sumber_dana ?? '-' }}</td>
                                        <td>{{ $row->nama_kriteria ?? '-' }}</td>
                                        <td>{{ $row->uraian }}</td>
                                        
                                        @if($showDebet)
                                            <td class="text-end">@currency($row->debet)</td>
                                        @endif
                                        
                                        <td class="text-end">@currency($row->kredit)</td>
                                        
                                        @if($showSaldoAkhir)
                                            <td class="text-end">@currency($row->saldo_akhir)</td>
                                        @endif
                                        
                                        @if($showSAP)
                                            <td class="text-center">{{ $row->no_sap ?? '-' }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="20" class="text-center text-muted py-3">
                                            Tidak ada data dengan filter yang dipilih
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            {{-- FOOTER: Total --}}
                            @if($data->count() > 0)
                                <tfoot class="table-secondary">
                                    @php
                                        // Hitung jumlah kolom dinamis
                                        $baseColumns = 4; // No, Tanggal, Penerima, Uraian
                                        $dynamicColumns = 0;
                                        
                                        if($bankTujuanId) $dynamicColumns++;
                                        if($sumberDanaIds && count($sumberDanaIds) > 0) $dynamicColumns++;
                                        if($kategoriIds && count($kategoriIds) > 0) $dynamicColumns++;
                                        if($idJenisPembayaran) $dynamicColumns++;
                                        if($showDebet) $dynamicColumns++;
                                        
                                        $totalColspan = $baseColumns + $dynamicColumns;
                                    @endphp
                                    
                                    {{-- Total Saldo Akhir (hanya untuk mode 0-1 filter) --}}
                                    @if($showSaldoAkhir)
                                        <tr>
                                            <th colspan="{{ $totalColspan }}" class="text-end">TOTAL SALDO AKHIR:</th>
                                            <th class="text-end">@currency($data->last()->saldo_akhir)</th>
                                            @if($showSAP)
                                                <th></th>
                                            @endif
                                        </tr>
                                    @endif
                                    
                                    {{-- Total Kredit --}}
                                    <tr>
                                        <th colspan="{{ $totalColspan }}" class="text-end">TOTAL KREDIT:</th>
                                        <th class="text-end">@currency($data->sum('kredit'))</th>
                                        @if($showSaldoAkhir)
                                            <th></th>
                                        @endif
                                        @if($showSAP)
                                            <th></th>
                                        @endif
                                    </tr>
                                    
                                    {{-- Total Debet (hanya untuk mode 0-1 filter) --}}
                                    @if($showDebet)
                                        <tr>
                                            <th colspan="{{ $totalColspan }}" class="text-end">TOTAL DEBET:</th>
                                            <th class="text-end">@currency($data->sum('debet'))</th>
                                            @if($showSaldoAkhir)
                                                <th></th>
                                            @endif
                                            @if($showSAP)
                                                <th></th>
                                            @endif
                                        </tr>
                                    @endif
                                </tfoot>
                            @endif
                        </table>
                    @endif
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
}

.kolom-kategori{
    width: 1%;
    white-space: nowrap;
    vertical-align: top;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('filterForm');

    // Sumber Dana Multi-select
    const sdItems = document.querySelectorAll('.sd-item');
    const sdAll = document.getElementById('sdAll');
    const sdText = document.getElementById('sdText');

    function updateSdText() {
        const checked = document.querySelectorAll('.sd-item:checked').length;
        sdText.textContent = checked ? checked + ' dipilih' : 'Semua Sumber Dana';
    }

    sdAll.addEventListener('change', () => {
        sdItems.forEach(i => i.checked = sdAll.checked);
        updateSdText();
        submitForm();
    });

    sdItems.forEach(i => i.addEventListener('change', () => {
        updateSdText();
        submitForm();
    }));

    updateSdText();

    // Kategori Multi-select
    const kategoriItems = document.querySelectorAll('.kategori-item');
    const kategoriAll = document.getElementById('kategoriAll');
    const kategoriText = document.getElementById('kategoriText');

    function updateKategoriText() {
        const checked = document.querySelectorAll('.kategori-item:checked').length;
        kategoriText.textContent = checked ? checked + ' dipilih' : 'Semua Kategori';
    }

    kategoriAll.addEventListener('change', () => {
        kategoriItems.forEach(i => i.checked = kategoriAll.checked);
        updateKategoriText();
        submitForm();
    });

    kategoriItems.forEach(i => i.addEventListener('change', () => {
        updateKategoriText();
        submitForm();
    }));

    updateKategoriText();

    // Tanggal Multi-select
    const tanggalItems = document.querySelectorAll('.tanggal-item');
    const tanggalAll = document.getElementById('tanggalAll');
    const tanggalText = document.getElementById('tanggalText');

    function updateTanggalText() {
        const checked = document.querySelectorAll('.tanggal-item:checked').length;
        tanggalText.textContent = checked ? checked + ' dipilih' : 'Semua Tanggal';
    }

    tanggalAll.addEventListener('change', () => {
        tanggalItems.forEach(i => i.checked = tanggalAll.checked);
        updateTanggalText();
        submitForm();
    });

    tanggalItems.forEach(i => i.addEventListener('change', () => {
        updateTanggalText();
        submitForm();
    }));

    updateTanggalText();
});

// Fungsi submit form
function submitForm() {
    document.getElementById('filterForm').submit();
}

// Reset all filters
function resetAllFilters() {
    const form = document.getElementById('filterForm');
    const tahun = form.querySelector('[name="tahun"]')?.value;
    window.location.href = form.action + '?tahun=' + tahun;
}
</script>

@endsection