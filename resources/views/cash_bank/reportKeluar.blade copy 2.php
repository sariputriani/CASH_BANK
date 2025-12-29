@extends("layouts/index")
@section('content')

<div class="container-fluid mt-4">
    <h1 class="tittle">Report <span style="color: #FF7518">Bank Keluar</span></h1>
    <small>Ini daftar Report Bank Keluar</small>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <!-- <div class="d-flex justify-content-end align-items-center">
                        <div>
                            <button type="button" class=" btn btn-sm bg-primary btn-outline-secondary text-white" onclick="resetAllFilters()">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                        </div>
                    </div> -->
                    <form action="{{ route('bank-keluar.report') }}" method="GET" id="filterForm">
                        <div class="row g-3 align-items-end">

                            <!-- Tahun -->
                            <div class="col-md-3">
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select" onchange="submitWithFilter('tahun')">
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
                                <select name="bulan" class="form-select" onchange="submitWithFilter('bulan')">
                                    <option value="">Semua Bulan</option>
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
                                        <span id="tanggalText">Semua Tanggal</span>
                                    </button>

                                    <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside" style="position: absolute; z-index: 1055; max-height: 300px; overflow-y: auto;">
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
                                <select name="bank_tujuan" class="form-select" onchange="submitWithFilter('bank_tujuan')">
                                    <option value="">Semua Bank Tujuan</option>
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

                                    <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside" style="position: absolute; z-index: 1055;">
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

                            <!-- Rekapan -->
                            <div class="col-md-3">
                                <label class="form-label">Rekapan</label>
                                <select name="rekapanVA" class="form-select" onchange="submitWithFilter('rekapanVA')">
                                    <option value="">-- Pilih Rekapan --</option>
                                    <option value="va" {{ request('rekapanVA') == 'va' ? 'selected' : '' }}>Saldo VA</option>
                                    <option value="bank" {{ request('rekapanVA') == 'bank' ? 'selected' : '' }}>Saldo BANK</option>
                                    <option value="kategori-full" {{ request('rekapanVA') == 'kategori-full' ? 'selected' : '' }}>Kategori Full</option>
                                </select>
                            </div>

                            <!-- Jenis Pembayaran -->
                             
                            <div class="col-md-3">
                                <label class="form-label">Jenis Pembayaran</label>
                                <select name="id_jenis_pembayaran" class="form-select" onchange="submitWithFilter('jenis_pembayaran')">
                                    <option value="">Semua Jenis Pembayaran</option>
                                    @foreach($rekapJenisPembayaran as $rjs)
                                        @if($rjs->id_jenis_pembayaran)
                                            <option value="{{ $rjs->id_jenis_pembayaran }}"
                                                {{ request('id_jenis_pembayaran') == $rjs->id_jenis_pembayaran ? 'selected' : '' }}>
                                                {{ $rjs->nama_jenis_pembayaran }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside" style="position: absolute; z-index: 1055;">
                                <div class="form-check fw-bold">
                                    <input class="form-check-input" type="checkbox" id="sdAll">
                                    <label class="form-check-label">-- Pilih Option --</label>
                                </div>
                                <hr class="my-1">

                                    @foreach($jenisPembayaranList as $sd)
                                    <div class="form-check">
                                        <input class="form-check-input sd-item"
                                                type="checkbox"
                                                name="jenisPembayaran[]"
                                                value="{{ $sd->id_jenis_pembayaran }}"
                                                {{ in_array($sd->id_jenis_pembayaran, request('jenis_pembayaran', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $sd->nama_jenis_pembayaran }}</label>
                                    </div>
                                    @endforeach
                            </div>

                            <!-- Kategori -->
                             <div class="col-md-3">
                                <label class="form-label">Kategori</label>
                                <div class="dropdown">
                                    <button class="form-select text-start" type="button" data-bs-toggle="dropdown">
                                        <span id="kategoriText">Semua Kategori</span>
                                    </button>

                                    <div class="dropdown-menu w-100 p-2" data-bs-auto-close="outside" style="position: absolute; z-index: 1055;">
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
                                <a href="#" onclick="window.print()" class="btn-export m-3 bg-success" style="border-radius: 10px; padding:15px;color: white;background-color: blue">
                                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                                </a>   
                                <a href="#" onclick="window.print()" class="btn-export bg-danger" style="border-radius: 10px; padding:15px;color: white;background-color: green">
                                    <i class="bi bi-printer"></i> Export PDF
                                </a>
                                <button type="button" class=" btn btn-sm bg-primary btn-outline-secondary text-white" onclick="resetAllFilters()" style="margin:10px;">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>   
                            </div>
                        </div>
                    </div>
                     
                    @if(request('bank_tujuan'))
                    <div>
                        <p class="text-muted">
                            Menampilkan data untuk: 
                            <strong>{{ $bankTujuanList->where('id_bank_tujuan', request('bank_tujuan'))->first()->nama_tujuan ?? '' }}</strong>
                        </p>
                        
                        <table class="table table-bordered table-striped">
                            <thead class="text-center table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Bukti</th>
                                    <th>Bank Tujuan</th>
                                    <th>Sumber Dana</th>
                                    <th>Penerima</th>
                                    <th>Uraian</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Saldo Akhir</th>
                                    <th>No Input SAP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $row)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $row->nama_tujuan ?? '-' }}</td>
                                        <td>{{ $row->nama_sumber_dana ?? '-' }}</td>
                                        <td>{{ $row->penerima ?? '-'}}</td>
                                        <td>{{ $row->uraian }}</td>
                                        <td class="text-end">@currency($row->debet)</td>
                                        <td class="text-end">@currency($row->kredit)</td>
                                        <td class="text-end">@currency($row->saldo_akhir)</td>
                                        <td class="text-center">{{ $row->no_sap ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($data->count() > 0)
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-end">TOTAL:</th>
                                        <th class="text-end">@currency($data->last()->saldo_akhir)</th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                    @endif
              
                    @if(request('sumber_dana') && is_array(request('sumber_dana')) && count(request('sumber_dana')) > 0)
                        <p class="text-muted">
                            Menampilkan data untuk sumber dana:
                            @php
                                $selectedIds = request('sumber_dana');
                                $namaSumberDana = $sumberDanaList
                                    ->whereIn('id_sumber_dana', $selectedIds)
                                    ->pluck('nama_sumber_dana')
                                    ->toArray();
                            @endphp
                            <strong>{{ implode(', ', $namaSumberDana) }}</strong>
                        </p>
                        
                        <table class="table table-bordered table-striped">
                            <thead class="text-center table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Bukti</th>
                                    <th>Sumber Dana</th>
                                    <th>Bank Tujuan</th>
                                    <th>Kategori</th>
                                    <th>Sub Kategori</th>
                                    <th>Item Sub Kategori</th>
                                    <th>Penerima</th>
                                    <th>Uraian</th>
                                    <th>Debet</th>
                                    <th>Kredit</th>
                                    <th>Saldo Akhir</th>
                                    <th>No Input SAP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $row)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $row->nama_sumber_dana ?? '-' }}</td>
                                        <td>{{ $row->nama_tujuan ?? '-' }}</td>
                                        <td>{{ $row->nama_kriteria ?? '-' }}</td>
                                        <td>{{ $row->nama_sub_kriteria ?? '-' }}</td>
                                        <td>{{ $row->nama_item_sub_kriteria ?? '-' }}</td>
                                        <td>{{ $row->penerima }}</td>
                                        <td>{{ $row->uraian }}</td>
                                        <td class="text-end">@currency($row->debet)</td>
                                        <td class="text-end">@currency($row->kredit)</td>
                                        <td class="text-end">@currency($row->saldo_akhir)</td>
                                        <td class="text-center">{{ $row->no_sap ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($data->count() > 0)
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="12" class="text-end table-danger">TOTAL Saldo Akhir Terakhir:</th>
                                        <th class="text-end table-danger">@currency($data->last()->saldo_akhir)</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    @endif

                    @if(request('jenis_pembayaran'))
                        <p class="text-muted">
                            Menampilkan data untuk jenis pembayaran:
                            <strong>{{ request('jenis_pembayaran') == '_null' ? 'Tanpa Jenis Pembayaran' : request('jenis_pembayaran') }}</strong>
                        </p>
                       
                        <table class="table table-bordered table-striped mt-3">
                            <thead class="text-center table-primary">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Agenda Tahun</th>
                                    <th>Uraian</th>
                                    <th>Penerima</th>
                                    <th>Sumber Dana</th>
                                    <th>Bank Tujuan</th>
                                    <th>Nama Kategori</th>
                                    <th>Sub Kategori</th>
                                    <th>Item Sub Kategori</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agendaData as $a)
                                    <tr>
                                        <td>{{ $a->tanggal ?? '-' }}</td>
                                        <td>{{ $a->agenda_tahun }}</td>
                                        <td>{{ $a->uraian }}</td>
                                        <td>{{ $a->penerima }}</td>
                                        <td>{{ $a->nama_sumber_dana }}</td>
                                        <td>{{ $a->nama_tujuan }}</td>
                                        <td>{{ $a->nama_kriteria }}</td>
                                        <td>{{ $a->nama_sub_kriteria }}</td>
                                        <td>{{ $a->nama_item_sub_kriteria }}</td>
                                        <td>{{ $a->jenis_pembayaran }}</td>
                                        <td class="text-end">{{ number_format($a->kredit, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">Data tidak ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($agendaData->count() > 0)
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th colspan="10" class="text-end table-danger">TOTAL:</th>
                                        <th class="text-end table-danger">
                                            {{ number_format($agendaData->sum('kredit'), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    @endif

                    {{-- TAMPILKAN REKAP SALDO VA / BANK --}}
                    @if(request('rekapanVA') && in_array(request('rekapanVA'), ['va', 'bank']))
                        <h5 class="mt-4">
                            Rekapan Saldo 
                            @if(request('rekapanVA') == 'va') Virtual Account (VA) @endif
                            @if(request('rekapanVA') == 'bank') Bank @endif
                        </h5>

                        <table class="table table-bordered table-striped mt-2">
                            <thead class=" text-center table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Bank / Akun VA</th>
                                    <th>Saldo VA</th>
                                    <th>Saldo SAP</th>
                                    <th>Selisih</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($rekapVA as $i => $row)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $row['bank'] ?? '-' }}</td>
                                        <td class="text-end">@currency($row['saldo_va'] ?? 0)</td>
                                        <td class="text-end">@currency($row['saldo_sap'] ?? 0)</td>
                                        <td class="text-end">@currency($row['selisih'] ?? 0)</td>
                                        <td>{{ $row['keterangan'] ?? '-' }}</td>
                                        
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-end fw-bold" colspan=2 >Jumlah</td>
                                    <td class="text-end fw-bold"> {{ number_format(collect($rekapVA)->sum('saldo_va'), 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold"> {{ number_format(collect($rekapVA)->sum('saldo_sap'), 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold"> {{ number_format(collect($rekapVA)->sum('selisih'), 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif

                    @if(request('rekapanVA') === 'kategori-full')

                        <h5 class="mt-4">Rekapan Kategori Full</h5>

                        <table class="table table-bordered table-striped mt-2">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>Kategori</th>
                                    <th>Sub Kriteria</th>
                                    <th>Item</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>

                            <tbody>

                            @foreach($rekapVA as $kategori => $subs)

                                @php
                                    // Hitung berapa total baris kategori (jumlah item + total sub + total kategori)
                                    $rowspanKategori = 0;
                                    $totalKategori = 0; 
                                    
                                    foreach ($subs as $sub => $items) {
                                        $rowspanKategori += count($items) + 1; 
                                        
                                        // Hitung total kategori
                                        foreach ($items as $item) {
                                            if (isset($item['kredit']) && is_numeric($item['kredit'])) {
                                                $totalKategori += floatval($item['kredit']);
                                            }
                                        }
                                    }
                                    
                                    $rowspanKategori += 1; 

                                    $firstKategoriPrinted = false;
                                @endphp


                                @foreach($subs as $sub => $items)

                                    @php
                                        $rowspanSub = count($items) + 1; 
                                        
                                   
                                        $totalSub = 0;
                                        if (is_array($items)) {
                                            foreach ($items as $item) {
                                                if (isset($item['kredit']) && is_numeric($item['kredit'])) {
                                                    $totalSub += floatval($item['kredit']);
                                                }
                                            }
                                        }
                                        
                                        $firstSubPrinted = false;
                                    @endphp

                                    @foreach($items as $item)
                                        <tr>
                                            {{-- Kategori tampil sekali --}}
                                            @if(!$firstKategoriPrinted)
                                                <td rowspan="{{ $rowspanKategori }}" class="">
                                                    {{ $kategori }}
                                                </td>
                                                @php $firstKategoriPrinted = true; @endphp
                                            @endif

                                            {{-- Sub tampil sekali --}}
                                            @if(!$firstSubPrinted)
                                                <td rowspan="{{ $rowspanSub }}" >
                                                    {{ $sub }}
                                                </td>
                                                @php $firstSubPrinted = true; @endphp
                                            @endif

                                            {{-- Item --}}
                                            <td>{{ $item['item'] ?? '-' }}</td>

                                            {{-- Kredit dengan Link --}}
                                            <!-- <td class="text-end">
                                                <a href="{{ route('detailItem.index', [
                                                    'kategori' => $kategori,
                                                    'sub' => $sub,
                                                    'item' => 'ALL',
                                                    'tahun' => request('tahun')
                                                ]) }}" 
                                                class="text-decoration-none text-dark ">
                                                    @currency($item['kredit'] ?? 0)
                                                </a>
                                            </td> -->
                                            <td class="text-end">
                                                    <a href="{{ route('detailItem.index', [
                                                        'kategori' => $kategori,
                                                        'sub' => $sub,
                                                        'item' => empty($item['item']) ? 'ALL' : $item['item'],
                                                        'tahun' => request('tahun')
                                                    ]) }}" 
                                                    class="text-decoration-none text-dark detail-link-bypass"
                                                    onclick="event.stopPropagation(); event.stopImmediatePropagation(); return true;">
                                                        @currency($item['kredit'] ?? 0)
                                                    </a>
                                                </td>
                                        </tr>
                                    @endforeach

                                    {{-- Total Sub --}}
                                    <tr>
                                        <td class="text-end" colspan="1">TOTAL {{ $sub }}</td>
                                        <td class="text-end">
                                            @php
                                                // Bersihkan karakter khusus dari kategori dan sub
                                                $cleanKategori = trim(preg_replace('/[\r\n\t]+/', ' ', $kategori));
                                                $cleanSub = trim(preg_replace('/[\r\n\t]+/', ' ', $sub));
                                                
                                                $urlDetailSub = route('detailSub.index', [
                                                    'kategori' => $cleanKategori,
                                                    'sub' => $cleanSub,
                                                    'item' => 'ALL',  
                                                    'tahun' => request('tahun')
                                                ]);
                                            @endphp
                                            
                                            <a href="{{ route('detailSub.index', [
                                                        'kategori' => $kategori,
                                                        'sub' => $sub,
                                                        'item' => 'ALL',
                                                        'tahun' => request('tahun')
                                                    ]) }}" 
                                                class="text-decoration-none text-success detail-link-bypass"
                                               onclick="event.stopPropagation(); event.stopImmediatePropagation(); return true;">
                                                        @currency($totalSub)
                                                    </a>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- TOTAL KATEGORI (ditambahkan di sini) --}}
                                <tr class="table-danger">
                                    <td class="text-end " colspan="2">
                                        TOTAL {{ strtoupper($kategori) }}
                                    </td>
                                    <td class="text-end ">
                                        @currency($totalKategori)</a>
                                    </td>
                                </tr>
                                
                            @endforeach

                            </tbody>
                        </table>

                    @endif

                    @if(request('kategori') && is_array(request('kategori')) && count(request('kategori')) > 0)
                        <h5 class="mt-4">Rekapan Kategori</h5>
                       
                        <table class="table table-bordered table-striped mt-2">
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
                                        <td class="text-end">@currency($row->total_kredit)</td> 
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Sumber Dana Multi-select
    const sdItems = document.querySelectorAll('.sd-item');
    const sdAll = document.getElementById('sdAll');
    const sdText = document.getElementById('sdText');
    const form = document.getElementById('filterForm');

    function updateSdText() {
        const checked = document.querySelectorAll('.sd-item:checked').length;
        sdText.textContent = checked ? checked + ' dipilih' : 'Semua Sumber Dana';
    }

    sdAll.addEventListener('change', () => {
        sdItems.forEach(i => i.checked = sdAll.checked);
        updateSdText();
        submitWithFilter('sumber_dana');
    });

    sdItems.forEach(i => i.addEventListener('change', () => {
        updateSdText();
        submitWithFilter('sumber_dana');
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
        form.submit();
    });

    kategoriItems.forEach(i => i.addEventListener('change', () => {
        updateKategoriText();
        form.submit();
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
        submitWithFilter('tanggal');
    });

    tanggalItems.forEach(i => i.addEventListener('change', () => {
        updateTanggalText();
        submitWithFilter('tanggal');
    }));

    updateTanggalText();
});

// Fungsi untuk submit form dengan reset filter lain
function submitWithFilter(activeFilter) {
    const form = document.getElementById('filterForm');
    const url = new URL(form.action);
    
    // Ambil nilai tahun (selalu dipertahankan)
    const tahun = form.querySelector('[name="tahun"]').value;
    if (tahun) {
        url.searchParams.set('tahun', tahun);
    }

    // Ambil nilai bulan jika ada
    const bulan = form.querySelector('[name="bulan"]').value;
    if (bulan) {
        url.searchParams.set('bulan', bulan);
    }
    // Ambil nilai bulan jika ada
    const selectedTanggal = form.querySelectorAll('.tanggal-item:checked');
        selectedTanggal.forEach(item => {
            url.searchParams.append('tanggal[]', item.value);
    });

    
    // Reset semua filter kecuali yang aktif
    if (activeFilter === 'tahun') {
        // Reset bulan dan tanggal saat tahun berubah
        form.querySelector('[name="bulan"]').value = '';
        const tanggalChecks = form.querySelectorAll('.tanggal-item');
        tanggalChecks.forEach(item => item.checked = false);
    } else if (activeFilter === 'bulan') {
        // Reset tanggal saat bulan berubah
        const tanggalChecks = form.querySelectorAll('.tanggal-item');
        tanggalChecks.forEach(item => item.checked = false);
    
    } else if (activeFilter === 'bank_tujuan') {
        const bankTujuan = form.querySelector('[name="bank_tujuan"]').value;
        if (bankTujuan) {
            url.searchParams.set('bank_tujuan', bankTujuan);
        }
    } else if (activeFilter === 'sumber_dana') {
        const checkedItems = form.querySelectorAll('.sd-item:checked');
        checkedItems.forEach(item => {
            url.searchParams.append('sumber_dana[]', item.value);
        });
    } else if (activeFilter === 'rekapanVA') {
        const rekapanVA = form.querySelector('[name="rekapanVA"]').value;
        if (rekapanVA) {
            url.searchParams.set('rekapanVA', rekapanVA);
        }
    } else if (activeFilter === 'jenis_pembayaran') {
        const jenisPembayaran = form.querySelector('[name="id_jenis_pembayaran"]').value;
        if (jenisPembayaran) {
            url.searchParams.set('id_jenis_pembayaran', jenisPembayaran);
        }
    }else if (activeFilter === 'kategori') {
        const checkedItems = form.querySelectorAll('.sd-item:checked');
        checkedItems.forEach(item => {
            url.searchParams.append('kategori[]', item.value);
        });
    }
    
    // Redirect ke URL baru
    window.location.href = url.toString();
}
// Reset all filters
function resetAllFilters() {
    const form = document.getElementById('filterForm');
    const tahun = form.querySelector('[name="tahun"]')?.value;
    
    // Redirect to base URL with only tahun parameter
    window.location.href = form.action + '?tahun=' + tahun;
}
</script>

<!-- // document.addEventListener('DOMContentLoaded', () => {
//     // PENTING: Bypass detail links DULU sebelum event listener lain
//     const detailLinks = document.querySelectorAll('.detail-link-bypass, a[href*="detail-sub"], a[href*="detail-item"], a[href*="detail-kategori"]');
    
//     detailLinks.forEach(link => {
//         link.addEventListener('click', (e) => {
//             e.stopPropagation();
//             e.stopImmediatePropagation();
//             // Link akan langsung navigate tanpa intercept
//             console.log('Detail link clicked:', link.href);
//         }, true); // true = capture phase, prioritas tertinggi
//     });

//     // Sumber Dana Multi-select
//     const sdItems = document.querySelectorAll('.sd-item');
//     const sdAll = document.getElementById('sdAll');
//     const sdText = document.getElementById('sdText');
//     const form = document.getElementById('filterForm');

//     function updateSdText() {
//         const checked = document.querySelectorAll('.sd-item:checked').length;
//         if (sdText) {
//             sdText.textContent = checked ? checked + ' dipilih' : 'Semua Sumber Dana';
//         }
//     }

//     if (sdAll) {
//         sdAll.addEventListener('change', () => {
//             sdItems.forEach(i => i.checked = sdAll.checked);
//             updateSdText();
//             submitWithFilter('sumber_dana');
//         });
//     }

//     sdItems.forEach(i => i.addEventListener('change', () => {
//         updateSdText();
//         submitWithFilter('sumber_dana');
//     }));

//     updateSdText();

//     // Kategori Multi-select
//     const kategoriItems = document.querySelectorAll('.kategori-item');
//     const kategoriAll = document.getElementById('kategoriAll');
//     const kategoriText = document.getElementById('kategoriText');

//     function updateKategoriText() {
//         const checked = document.querySelectorAll('.kategori-item:checked').length;
//         if (kategoriText) {
//             kategoriText.textContent = checked ? checked + ' dipilih' : 'Semua Kategori';
//         }
//     }

//     if (kategoriAll) {
//         kategoriAll.addEventListener('change', () => {
//             kategoriItems.forEach(i => i.checked = kategoriAll.checked);
//             updateKategoriText();
//             form.submit();
//         });
//     }

//     kategoriItems.forEach(i => i.addEventListener('change', () => {
//         updateKategoriText();
//         form.submit();
//     }));

//     updateKategoriText();

//     // Tanggal Multi-select
//     const tanggalItems = document.querySelectorAll('.tanggal-item');
//     const tanggalAll = document.getElementById('tanggalAll');
//     const tanggalText = document.getElementById('tanggalText');

//     function updateTanggalText() {
//         const checked = document.querySelectorAll('.tanggal-item:checked').length;
//         if (tanggalText) {
//             tanggalText.textContent = checked ? checked + ' dipilih' : 'Semua Tanggal';
//         }
//     }

//     if (tanggalAll) {
//         tanggalAll.addEventListener('change', () => {
//             tanggalItems.forEach(i => i.checked = tanggalAll.checked);
//             updateTanggalText();
//             submitWithFilter('tanggal');
//         });
//     }

//     tanggalItems.forEach(i => i.addEventListener('change', () => {
//         updateTanggalText();
//         submitWithFilter('tanggal');
//     }));

//     updateTanggalText();
// }); -->
<!-- </script> -->

<!-- // document.addEventListener('DOMContentLoaded', () => {

//     const form = document.getElementById('filterForm');

//     const qs  = s => document.querySelector(s);
//     const qsa = s => document.querySelectorAll(s);

//     const bulanSelect   = qs('[name="bulan"]');
//     const tanggalItems  = qsa('.tanggal-item');
//     const tanggalText   = qs('#tanggalText');

//     /* ================= HELPER ================= */
//     const hasMainFilter = () =>
//         qsa('.sd-item:checked').length ||
//         qsa('.kategori-item:checked').length ||
//         qs('[name="bank_tujuan"]')?.value ||
//         qs('[name="rekapanVA"]')?.value ||
//         qs('[name="id_jenis_pembayaran"]')?.value;

//     const submit = () => form.submit();

//     /* ================= STATE CONTROL ================= */
//     function updateFilterState() {

//         const main = hasMainFilter();
//         const bulan = bulanSelect?.value;

//         // BULAN
//         bulanSelect.disabled = !main;

//         // TANGGAL
//         tanggalItems.forEach(i => {
//             i.disabled = !main || !bulan;
//             if (!bulan) i.checked = false;
//         });

//         if (!bulan && tanggalText) {
//             tanggalText.textContent = 'Semua Tanggal';
//         }
//     }

//     updateFilterState();

//     /* ================= MAIN FILTER ================= */
//     [
//         'bank_tujuan',
//         'rekapanVA',
//         'id_jenis_pembayaran'
//     ].forEach(name => {
//         const el = qs(`[name="${name}"]`);
//         if (el) el.addEventListener('change', submit);
//     });

//     qsa('.sd-item, .kategori-item').forEach(i => {
//         i.addEventListener('change', submit);
//     });

//     /* ================= BULAN ================= */
//     bulanSelect?.addEventListener('change', () => {
//         updateFilterState();
//         submit();
//     });

//     /* ================= TANGGAL ================= */
//     tanggalItems.forEach(i => {
//         i.addEventListener('change', () => {
//             if (!bulanSelect.value) return;
//             submit();
//         });
//     });

// });


// // Fungsi untuk submit form dengan reset filter lain
// function submitWithFilter(activeFilter) {
//     const form = document.getElementById('filterForm');
//     const url = new URL(form.action);
    
//     // Ambil nilai tahun (selalu dipertahankan)
//     const tahun = form.querySelector('[name="tahun"]').value;
//     if (tahun) {
//         url.searchParams.set('tahun', tahun);
//     }

//     // Ambil nilai bulan jika ada
//     const bulan = form.querySelector('[name="bulan"]').value;
//     if (bulan) {
//         url.searchParams.set('bulan', bulan);
//     }
    
//     // Ambil nilai tanggal jika ada
//     const selectedTanggal = form.querySelectorAll('.tanggal-item:checked');
//     selectedTanggal.forEach(item => {
//         url.searchParams.append('tanggal[]', item.value);
//     });

//     // Reset semua filter kecuali yang aktif
//     if (activeFilter === 'tahun') {
//         form.querySelector('[name="bulan"]').value = '';
//         const tanggalChecks = form.querySelectorAll('.tanggal-item');
//         tanggalChecks.forEach(item => item.checked = false);
//     } else if (activeFilter === 'bulan') {
//         const tanggalChecks = form.querySelectorAll('.tanggal-item');
//         tanggalChecks.forEach(item => item.checked = false);
//     } else if (activeFilter === 'bank_tujuan') {
//         const bankTujuan = form.querySelector('[name="bank_tujuan"]').value;
//         if (bankTujuan) {
//             url.searchParams.set('bank_tujuan', bankTujuan);
//         }
//     } else if (activeFilter === 'sumber_dana') {
//         const checkedItems = form.querySelectorAll('.sd-item:checked');
//         checkedItems.forEach(item => {
//             url.searchParams.append('sumber_dana[]', item.value);
//         });
//     } else if (activeFilter === 'rekapanVA') {
//         const rekapanVA = form.querySelector('[name="rekapanVA"]').value;
//         if (rekapanVA) {
//             url.searchParams.set('rekapanVA', rekapanVA);
//         }
//     } else if (activeFilter === 'jenis_pembayaran') {
//         const jenisPembayaran = form.querySelector('[name="id_jenis_pembayaran"]').value;
//         if (jenisPembayaran) {
//             url.searchParams.set('id_jenis_pembayaran', jenisPembayaran);
//         }
//     } else if (activeFilter === 'kategori') {
//         const checkedItems = form.querySelectorAll('.kategori-item:checked');
//         checkedItems.forEach(item => {
//             url.searchParams.append('kategori[]', item.value);
//         });
//     }
    
//     // Redirect ke URL baru
//     window.location.href = url.toString();
// }

// // Reset all filters
// function resetAllFilters() {
//     const form = document.getElementById('filterForm');
//     const tahun = form.querySelector('[name="tahun"]')?.value;
    
//     // Redirect to base URL with only tahun parameter
//     window.location.href = form.action + '?tahun=' + tahun;
// }
</script> -->

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
</style>


{{-- MODAL FILTER --}}
@include('cash_bank.modal.modalFilter')

@endsection