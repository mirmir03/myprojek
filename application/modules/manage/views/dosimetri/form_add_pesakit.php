<div class="col-lg-12">
    <div class="card">
        <div class="px-4 py-3 border-bottom">
            <h5 class="card-title fw-semibold mb-0">REKOD DOSIMETRI PESAKIT</h5>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger mx-4 mt-3">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= module_url('dosimetriPesakit/add/' . $pesakit->T01_ID_PESAKIT) ?>" enctype="multipart/form-data">
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

            <!-- Voltan Tiub -->
            <div class="mb-4 row align-items-center">
                <label for="tube_voltage" class="form-label fw-semibold col-sm-3 col-form-label text-end">Voltan Tiub (kV)</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" id="tube_voltage" name="tube_voltage" min="40" max="150" required>
                    <small class="form-text text-muted">Julat sah: 40 – 150 kV</small>
                </div>
            </div>

            <!-- Arus-Masa -->
            <div class="mb-4 row align-items-center">
                <label for="current_time_product" class="form-label fw-semibold col-sm-3 col-form-label text-end">Arus-Masa (mAs)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control" id="current_time_product" name="current_time_product" min="0.5" max="600" required>
                    <small class="form-text text-muted">Julat sah: 0.5 – 600 mAs</small>
                </div>
            </div>

            <!-- Masa Pendedahan -->
            <div class="mb-4 row align-items-center">
                <label for="exposure_time" class="form-label fw-semibold col-sm-3 col-form-label text-end">Masa Pendedahan (ms)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="exposure_time" name="exposure_time" min="500" max="4000" required>
                    <small class="form-text text-muted">Julat sah: 500 – 4000 ms</small>
                </div>
            </div>

            <!-- Jarak Sumber-Gambar -->
            <div class="mb-4 row align-items-center">
                <label for="source_image_distance" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jarak Sumber-Gambar (cm)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="source_image_distance" name="source_image_distance" min="90" max="200" required>
                    <small class="form-text text-muted">Julat sah: 90 – 200 cm</small>
                </div>
            </div>

            <!-- Jarak Sumber-Kulit -->
            <div class="mb-4 row align-items-center">
                <label for="source_skin_distance" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jarak Sumber-Kulit (cm)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="source_skin_distance" name="source_skin_distance" min="30" max="100" required>
                    <small class="form-text text-muted">Julat sah: 30 – 100 cm</small>
                </div>
            </div>

            <!-- Saiz Medan Kolimasi -->
            <div class="mb-4 row align-items-center">
                <label for="collimation_field_size" class="form-label fw-semibold col-sm-3 col-form-label text-end">Saiz Medan Kolimasi</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="collimation_field_size" name="collimation_field_size" placeholder="contoh: 43x43 cm" required>
                    <small class="form-text text-muted">Format contoh: 43x43 cm</small>
                </div>
            </div>

            <!-- Grid -->
            <div class="mb-4 row align-items-center">
                <label for="grid" class="form-label fw-semibold col-sm-3 col-form-label text-end">Grid</label>
                <div class="col-sm-9">
                    <select class="form-select" id="grid" name="grid" required>
                        <option value="" selected disabled>Sila Pilih</option>
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </div>
            </div>

            <!-- DAP -->
            <div class="mb-4 row align-items-center">
                <label for="dose_area_product" class="form-label fw-semibold col-sm-3 col-form-label text-end">DAP (μGy·m²)</label>
                <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control" id="dose_area_product" name="dose_area_product">
                    <small class="form-text text-muted">Masukkan nilai DAP jika tersedia (dalam μGy·m²)</small>
                </div>
            </div>

            <!-- Indeks Pendedahan -->
            <div class="mb-4 row align-items-center">
                <label for="exposure_index" class="form-label fw-semibold col-sm-3 col-form-label text-end">Indeks Pendedahan</label>
                <div class="col-sm-9">
                    <input type="number" step="0.1" class="form-control" id="exposure_index" name="exposure_index" min="100" max="9000" placeholder="Min 100 - Max 9000">
                    <small class="form-text text-muted">Nilai sah antara 100 hingga 9000</small>
                </div>
            </div>

            <!-- Hidden Field -->
            <input type="hidden" name="T01_ID_PESAKIT" value="<?= $pesakit->T01_ID_PESAKIT ?>">

            <!-- Buttons -->
            <div class="row mb-4">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Simpan Rekod</button>
                        <a href="<?= module_url('dosimetriPesakit/form_add_pesakit') ?>" class="btn bg-danger-subtle text-danger">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
