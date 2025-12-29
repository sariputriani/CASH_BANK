<!-- Modal -->
<div class="modal fade" id="modalFilter" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h5 class="modal-title text-white" id="exampleModalLabel"><i class="fas fa-search"></i>Filter Tanggal </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('bank-keluar.report') }}" method="GET">
            <div class="form-group">
                <label>Tanggal Awal</label>
                <input type="date" name="tanggal_awal"
                    value="{{ request('tanggal_awal') }}"
                    class="form-control">
            </div>

            <div class="form-group mt-3">
                <label>Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir"
                    value="{{ request('tanggal_akhir') }}"
                    class="form-control">
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
            </div>
        </form>

      </div>
    </div>
  </div>
</div>