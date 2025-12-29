<div class="modal fade" id="editSAP" tabindex="-1">
  <div class="modal-dialog">
    <form id="formSAP">
      @csrf

      <input type="hidden" id="id_bank_keluar">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Input No SAP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <!-- <input type="text" class="form-control" id="no_sap" name="no_sap"> -->
           <input type="text"
       class="form-control"
       id="no_sap"
       name="no_sap"
       placeholder="Masukkan No SAP">
        </div>

        <div class="modal-footer">
          <button class="btn bg-primary text-white">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
