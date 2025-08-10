<div class="col-lg-12">
    <div class="card">
        <div class="px-4 py-3 border-bottom">
            <h5 class="card-title fw-semibold mb-0">KEMASKINI REKOD DOSIMETRI PESAKIT</h5>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger mx-4 mt-3">
            <?= $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('manage/dosimetriPesakit/update/' . $record->T03_ID_DOS_PESAKIT) ?>">


            <!-- Patient Info (Readonly) -->
            <div class="mb-4 row align-items-center mt-3">
                <label class="form-label fw-semibold col-sm-3 col-form-label text-end">No Rujukan</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($pesakit->T01_NO_RUJUKAN) ?>" readonly>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pesakit</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($pesakit->T01_NAMA_PESAKIT) ?>" readonly>
                </div>
            </div>

            <!-- Dosimetry Parameters -->
            <div class="mb-4 row align-items-center">
                <label for="tube_voltage" class="form-label fw-semibold col-sm-3 col-form-label text-end">Voltan Tiub (kV)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="tube_voltage" name="tube_voltage"
                        value="<?= htmlspecialchars($record->T03_TUBE_VOLTAGE) ?>" min="40" max="150" required>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="current_time_product" class="form-label fw-semibold col-sm-3 col-form-label text-end">Arus-Masa (mAs)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="current_time_product" name="current_time_product"
                        value="<?= htmlspecialchars($record->T03_CURRENT_TIME_PRODUCT) ?>" min="0.5" max="600" step="0.01" required>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="exposure_time" class="form-label fw-semibold col-sm-3 col-form-label text-end">Masa Pendedahan (ms)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="exposure_time" name="exposure_time"
                        value="<?= htmlspecialchars($record->T03_EXPOSURE_TIME) ?>" min="500" max="4000" step="0.1" required>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="source_image_distance" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jarak Sumber-Gambar (cm)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="source_image_distance" name="source_image_distance"
                        value="<?= htmlspecialchars($record->T03_SOURCE_IMAGE_DISTANCE) ?>" min="90" max="200" required>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="source_skin_distance" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jarak Sumber-Kulit (cm)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="source_skin_distance" name="source_skin_distance"
                        value="<?= htmlspecialchars($record->T03_SOURCE_SKIN_DISTANCE) ?>" min="30" max="100" required>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="collimation_field_size" class="form-label fw-semibold col-sm-3 col-form-label text-end">Saiz Medan Kolimasi</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="collimation_field_size" name="collimation_field_size"
                        value="<?= htmlspecialchars($record->T03_COLLIMATION_FIELD_SIZE) ?>" placeholder="contoh: 43x43 cm" required>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="grid" class="form-label fw-semibold col-sm-3 col-form-label text-end">Grid</label>
                <div class="col-sm-9">
                    <select class="form-select" id="grid" name="grid" required>
                        <option value="" disabled>Sila Pilih</option>
                        <option value="Ya" <?= $record->T03_GRID == "Ya" ? "selected" : "" ?>>Ya</option>
                        <option value="Tidak" <?= $record->T03_GRID == "Tidak" ? "selected" : "" ?>>Tidak</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="dose_area_product" class="form-label fw-semibold col-sm-3 col-form-label text-end">DAP (μGy·m²)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control" id="dose_area_product" name="dose_area_product"
                        value="<?= htmlspecialchars($record->T03_DOSE_AREA_PRODUCT) ?>">
                </div>
            </div>

            <div class="mb-4 row align-items-center">
                <label for="exposure_index" class="form-label fw-semibold col-sm-3 col-form-label text-end">Indeks Pendedahan</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="exposure_index" name="exposure_index"
                        value="<?= htmlspecialchars($record->T03_EXPOSURE_INDEX) ?>">
                </div>
            </div>

            <!-- Hidden IDs -->
            <input type="hidden" name="T03_ID_DOS_PESAKIT" value="<?= $record->T03_ID_DOS_PESAKIT ?>">
            <input type="hidden" name="T01_ID_PESAKIT" value="<?= $record->T01_ID_PESAKIT ?>">

            <!-- Buttons -->
            <div class="row mb-4">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Kemaskini Rekod</button>
                        <a href="<?= module_url('dosimetriPesakit/listdospesakit') ?>" class="btn bg-danger-subtle text-danger">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("tube_voltage").focus();

    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && isNaN(this.value)) {
                alert('Sila masukkan nilai angka sahaja');
                this.value = '';
                this.focus();
            }
        });
    });
});
</script>
