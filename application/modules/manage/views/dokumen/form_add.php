<form method="POST" action="<?php echo module_url('dokumen/add'); ?>" enctype="multipart/form-data">
    <div class="col-lg-12">
        <div class="card">
            <div class="px-4 py-3 border-bottom">
                <h5 class="card-title fw-semibold mb-0">MUAT NAIK DOKUMEN</h5>
            </div>
            
            <!-- Tarikh -->
            <div class="mb-4 row align-items-center">
                <label for="tarikh" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="tarikh" name="tarikh" required>
                </div>
            </div>

            <!-- Reject Analysis -->
            <div class="mb-4 row align-items-center">
                <label for="dokumen_reject" class="form-label fw-semibold col-sm-3 col-form-label text-end">Reject Analysis</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="dokumen_reject" name="dokumen_reject" >
                </div>
            </div>

            <!-- CME Certification -->
            <div class="mb-4 row align-items-center">
                <label for="dokumen_certificate" class="form-label fw-semibold col-sm-3 col-form-label text-end">CME Certification</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="dokumen_certificate" name="dokumen_certificate" >
                </div>
            </div>

            <!-- Audit Image -->
            <div class="mb-4 row align-items-center">
                <label for="dokumen_audit" class="form-label fw-semibold col-sm-3 col-form-label text-end">Audit Image</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="dokumen_audit" name="dokumen_audit" >
                </div>
            </div>

            <!-- Laporan QC -->
            <div class="mb-4 row align-items-center">
                <label for="dokumen_qc" class="form-label fw-semibold col-sm-3 col-form-label text-end">Laporan QC</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" id="dokumen_qc" name="dokumen_qc" >
                </div>
            </div>

            <!-- Buttons -->
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="d-flex align-items-center gap-6">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn bg-danger-subtle text-danger" onclick="history.back();">Batal</button>
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