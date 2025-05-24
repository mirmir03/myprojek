<form method="POST" action="<?php echo module_url("dokumen/save/".$dokumen->T02_ID_DOKUMEN) ?>" enctype="multipart/form-data">
  <div class="col-lg-12">
    <div class="card">
      <div class="px-4 py-3 border-bottom">
        <h5 class="card-title fw-semibold mb-0">EDIT DOKUMEN</h5>
      </div>

      <!-- Tarikh Field -->
      <div class="mb-4 row align-items-center">
        <label for="tarikh" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh</label>
        <div class="col-sm-9">
          <input type="date" class="form-control" id="tarikh" name="tarikh" 
          value="<?php echo date('Y-m-d', strtotime($dokumen->T02_TARIKH)); ?>" required>
        </div>
      </div>

      <!-- Reject Analysis File -->
      <div class="mb-4 row align-items-center">
        <label for="dokumen_reject" class="form-label fw-semibold col-sm-3 col-form-label text-end">Reject Analysis</label>
        <div class="col-sm-9">
          <input type="file" class="form-control" id="dokumen_reject" name="dokumen_reject">
          <?php if (!empty($dokumen->T02_DOKUMEN_REJECT_ANALYSIS)): ?>
            <div class="mt-2">
              <a href="<?= base_url('www-uploads/' . basename($dokumen->T02_DOKUMEN_REJECT_ANALYSIS)); ?>" target="_blank" class="btn btn-link">
                <?= basename($dokumen->T02_DOKUMEN_REJECT_ANALYSIS); ?>
              </a>
            </div>
          <?php else: ?>
            <div class="mt-2 text-muted">No file uploaded</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- CME Certificate File -->
      <div class="mb-4 row align-items-center">
        <label for="dokumen_certificate" class="form-label fw-semibold col-sm-3 col-form-label text-end">Cme Certificate</label>
        <div class="col-sm-9">
          <input type="file" class="form-control" id="dokumen_certificate" name="dokumen_certificate">
          <?php if (!empty($dokumen->T02_DOKUMEN_CME_CERTIFICATION)): ?>
            <div class="mt-2">
              <a href="<?= base_url('www-uploads/' . basename($dokumen->T02_DOKUMEN_CME_CERTIFICATION)); ?>" target="_blank" class="btn btn-link">
                <?= basename($dokumen->T02_DOKUMEN_CME_CERTIFICATION); ?>
              </a>
            </div>
          <?php else: ?>
            <div class="mt-2 text-muted">No file uploaded</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Audit Image -->
      <div class="mb-4 row align-items-center">
        <label for="dokumen_audit" class="form-label fw-semibold col-sm-3 col-form-label text-end">Audit Image</label>
        <div class="col-sm-9">
          <input type="file" class="form-control" id="dokumen_audit" name="dokumen_audit">
          <?php if (!empty($dokumen->T02_AUDIT_IMAGE)): ?>
            <div class="mt-2">
              <a href="<?= base_url('www-uploads/' . basename($dokumen->T02_AUDIT_IMAGE)); ?>" target="_blank" class="btn btn-link">
                <?= basename($dokumen->T02_AUDIT_IMAGE); ?>
              </a>
            </div>
          <?php else: ?>
            <div class="mt-2 text-muted">No file uploaded</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Laporan QC -->
      <div class="mb-4 row align-items-center">
        <label for="dokumen_qc" class="form-label fw-semibold col-sm-3 col-form-label text-end">Laporan QC</label>
        <div class="col-sm-9">
          <input type="file" class="form-control" id="dokumen_qc" name="dokumen_qc">
          <?php if (!empty($dokumen->T02_DOKUMEN_LAPORANQC)): ?>
            <div class="mt-2">
              <a href="<?= base_url('www-uploads/' . basename($dokumen->T02_DOKUMEN_LAPORANQC)); ?>" target="_blank" class="btn btn-link">
                <?= basename($dokumen->T02_DOKUMEN_LAPORANQC); ?>
              </a>
            </div>
          <?php else: ?>
            <div class="mt-2 text-muted">No file uploaded</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="row">
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
          <div class="d-flex align-items-center gap-6">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn bg-danger-subtle text-danger" 
            onclick="window.location='<?php echo module_url('dokumen/listdokumen')?>'">Batal</button>
          </div>
        </div>
      </div>

    </div>
  </div>
</form>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("tarikh");
    const today = new Date();
    
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    const todayStr = `${yyyy}-${mm}-${dd}`;
    
    const nextDay = new Date(today);
    nextDay.setDate(today.getDate() + 1);
    const nextDayStr = `${nextDay.getFullYear()}-${String(nextDay.getMonth() + 1).padStart(2, "0")}-${String(nextDay.getDate()).padStart(2, "0")}`;
    
    dateInput.min = todayStr;
    dateInput.max = nextDayStr;
});

</script>


