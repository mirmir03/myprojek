<form method="POST" action="<?php echo module_url("reject/save/".$reject->T06_ID_REJECT)?>" enctype="multipart/form-data">
  <div class="col-lg-12">
    <div class="card">
      <div class="px-4 py-3 border-bottom">
        <h5 class="card-title fw-semibold mb-0">EDIT REJECT</h5>
      </div>
      
      <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger mx-4 mt-3">
          <?php echo $this->session->flashdata('error'); ?>
      </div>
      <?php endif; ?>
      
      <!-- Tarikh -->
      <div class="mb-4 row align-items-center mt-3">
        <label for="tarikh" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh</label>
        <div class="col-sm-9">
          <input type="date" class="form-control" id="tarikh" name="tarikh" 
                 value="<?php echo isset($reject->T06_TARIKH) ? date('Y-m-d', strtotime($reject->T06_TARIKH)) : date('Y-m-d'); ?>" 
                 required>
        </div>
      </div>

      <!-- Jenis Reject -->
      <div class="mb-4 row align-items-center">
        <label for="jenis_reject" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jenis Reject</label>
        <div class="col-sm-9">
          <select class="form-select" id="jenis_reject" name="jenis_reject" required>
            <option value="" disabled <?php echo empty($reject->T06_JENIS_REJECT) ? 'selected' : ''; ?>>Sila Pilih</option>
            <option value="OverExposure" <?php echo $reject->T06_JENIS_REJECT === 'OverExposure' ? 'selected' : ''; ?>>OverExposure</option>
            <option value="UnderExposure" <?php echo $reject->T06_JENIS_REJECT === 'UnderExposure' ? 'selected' : ''; ?>>UnderExposure</option>
            <option value="Double Exposure" <?php echo $reject->T06_JENIS_REJECT === 'Double Exposure' ? 'selected' : ''; ?>>Double Exposure</option>
            <option value="Wrong Technique" <?php echo $reject->T06_JENIS_REJECT === 'Wrong Technique' ? 'selected' : ''; ?>>Wrong Technique</option>
            <option value="Wrong Patient / Exam" <?php echo $reject->T06_JENIS_REJECT === 'Wrong Patient / Exam' ? 'selected' : ''; ?>>Wrong Patient / Exam</option>
            <option value="Wrong Marker" <?php echo $reject->T06_JENIS_REJECT === 'Wrong Marker' ? 'selected' : ''; ?>>Wrong Marker</option>
            <option value="Collimation Error" <?php echo $reject->T06_JENIS_REJECT === 'Collimation Error' ? 'selected' : ''; ?>>Collimation Error</option>
            <option value="Patient Movement" <?php echo $reject->T06_JENIS_REJECT === 'Patient Movement' ? 'selected' : ''; ?>>Patient Movement</option>
            <option value="Patient Related Artifact" <?php echo $reject->T06_JENIS_REJECT === 'Patient Related Artifact' ? 'selected' : ''; ?>>Patient Related Artifact</option>
            <option value="Equipment Fault" <?php echo $reject->T06_JENIS_REJECT === 'Equipment Fault' ? 'selected' : ''; ?>>Equipment Fault</option>
            <option value="Detector / imaging plate" <?php echo $reject->T06_JENIS_REJECT === 'Detector / imaging plate' ? 'selected' : ''; ?>>Detector / imaging plate</option>
            <option value="Image artifact" <?php echo $reject->T06_JENIS_REJECT === 'Image artifact' ? 'selected' : ''; ?>>Image artifact</option>
            <option value="Processing Fault" <?php echo $reject->T06_JENIS_REJECT === 'Processing Fault' ? 'selected' : ''; ?>>Processing Fault</option>
          </select>
        </div>
      </div>

      <!-- Buttons -->
      <div class="row mb-4">
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
          <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn bg-danger-subtle text-danger" onclick="window.location='<?php echo module_url('reject/listreject')?>'">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Form validation before submit
    document.querySelector("form").addEventListener("submit", function(event) {
        const jenisReject = document.getElementById("jenis_reject").value;
        const tarikh = document.getElementById("tarikh").value;

        if (!jenisReject) {
            event.preventDefault();
            alert("Sila pilih jenis reject.");
            document.getElementById("jenis_reject").focus();
            return false;
        }

        if (!tarikh) {
            event.preventDefault();
            alert("Sila pilih tarikh.");
            document.getElementById("tarikh").focus();
            return false;
        }
    });
});
</script>