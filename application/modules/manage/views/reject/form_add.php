<form method="POST" action="<?php echo module_url('reject/add'); ?>" enctype="multipart/form-data">
    <div class="col-lg-12">
        <div class="card">
            <div class="px-4 py-3 border-bottom">
                <h5 class="card-title fw-semibold mb-0">TAMBAH REJECT BAHARU</h5>
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
                    <input type="date" class="form-control" id="tarikh" name="tarikh" required>
                </div>
            </div>

            <!-- Jenis Reject -->
            <div class="mb-4 row align-items-center">
                <label for="jenis_reject" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jenis Reject</label>
                <div class="col-sm-9">
                    <select class="form-select" id="jenis_reject" name="jenis_reject" required>
                        <option value="">Sila Pilih</option>
                        <option value="OverExposure">OverExposure</option>
                        <option value="UnderExposure">UnderExposure</option>
                        <option value="Double Exposure">Double Exposure</option>
                        <option value="Wrong Technique">Wrong Technique</option>
                        <option value="Wrong Patient / Exam">Wrong Patient / Exam</option>
                        <option value="Wrong Marker">Wrong Marker</option>
                        <option value="Collimation Error">Collimation Error</option>
                        <option value="Patient Movement">Patient Movement</option>
                        <option value="Patient Related Artifact">Patient Related Artifact</option>
                        <option value="Equipment Fault">Equipment Fault</option>
                        <option value="Detector / imaging plate">Detector / imaging plate</option>
                        <option value="Image artifact">Image artifact</option>
                        <option value="Processing Fault">Processing Fault</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="row mb-4">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn bg-danger-subtle text-danger" onclick="history.back();">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateInput = document.getElementById("tarikh");
    const today = new Date();
   
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    const todayStr = `${yyyy}-${mm}-${dd}`;
   
    // Set the initial value to today (but allow editing)
    dateInput.value = todayStr;

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