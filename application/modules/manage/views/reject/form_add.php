<form method="POST" action="<?php echo module_url('reject/add'); ?>">
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
        
        <form method="POST" action="<?php echo module_url('reject/add'); ?>" id="reject-form">
            <!-- CSRF Protection -->
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            
            <!-- Staff Information (hidden if using session, visible for testing) -->
            <?php if (ENVIRONMENT === 'development'): ?>
            <div class="mb-4 row align-items-center mt-3">
                <label for="staff_id" class="form-label fw-semibold col-sm-3 col-form-label text-end">Staff ID</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="staff_id" name="staff_id" 
                           value="<?php echo $this->session->userdata('staff_id') ?? 'XRAY001'; ?>">
                </div>
            </div>
            <?php endif; ?>

            <!-- Jenis Reject -->
            <div class="mb-4 row align-items-center mt-3">
                <label for="jenis_reject" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jenis Reject <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <select class="form-select" id="jenis_reject" name="jenis_reject" required>
                        <option value="">- Sila Pilih -</option>
                        <option value="Overpressure">Overpressure</option>
                        <option value="Underfill">Underfill</option>
                        <option value="Misalignment">Misalignment</option>
                        <option value="Contamination">Contamination</option>
                        <option value="Crack">Crack</option>
                        <option value="Delamination">Delamination</option>
                        <option value="Short Circuit">Short Circuit</option>
                        <option value="Open Circuit">Open Circuit</option>
                        <option value="Solder Bridge">Solder Bridge</option>
                        <option value="Component Missing">Component Missing</option>
                        <option value="Wrong Component">Wrong Component</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                    <div class="invalid-feedback">Sila pilih jenis reject</div>
                </div>
            </div>

            <!-- Tarikh -->
            <div class="mb-4 row align-items-center">
                <label for="tarikh" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="datetime-local" class="form-control" id="tarikh" name="tarikh" required>
                    <div class="invalid-feedback">Sila masukkan tarikh yang sah</div>
                </div>
            </div>

            <!-- Catatan (Optional) -->
            <div class="mb-4 row align-items-center">
                <label for="catatan" class="form-label fw-semibold col-sm-3 col-form-label text-end">Catatan</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="row mb-4">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Simpan
                        </button>
                        <a href="<?php echo module_url('reject/listreject'); ?>" class="btn bg-danger-subtle text-danger">
                            <i class="ti ti-x me-1"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Set default datetime to now
    const now = new Date();
    const timezoneOffset = now.getTimezoneOffset() * 60000;
    const localISOTime = (new Date(now - timezoneOffset)).toISOString().slice(0, 16);
    document.getElementById('tarikh').value = localISOTime;

    // Form validation
    const form = document.getElementById('reject-form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Dynamic "Lain-lain" input
    const jenisReject = document.getElementById('jenis_reject');
    const catatanField = document.getElementById('catatan');
    
    jenisReject.addEventListener('change', function() {
        if (this.value === 'Lain-lain') {
            catatanField.setAttribute('required', '');
            catatanField.parentElement.querySelector('label').innerHTML += ' <span class="text-danger">*</span>';
        } else {
            catatanField.removeAttribute('required');
            catatanField.parentElement.querySelector('label').innerHTML = 
                catatanField.parentElement.querySelector('label').innerHTML.replace(' <span class="text-danger">*</span>', '');
        }
    });
});
</script>