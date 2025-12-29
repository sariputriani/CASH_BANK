<div class="modal fade" id="editBankTujuan" tabindex="-1">
  <div class="modal-dialog">
    <form id="formEditBankTujuan" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <input type="hidden" id="id_bank_tujuan">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Bank Tujuan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="text" class="form-control"  id="edit_nama_tujuan" name="nama_tujuan" value="{{ $row->nama_tujuan }}">
        </div>

        <div class="modal-footer">
          <button class="btn bg-primary text-white">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('editBankTujuan');

    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        const id   = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');

        document.getElementById('edit_nama_tujuan').value = nama;

        document.getElementById('formEditBankTujuan').action =
            `/daftarBank/${id}`;
    });
});
</script>
