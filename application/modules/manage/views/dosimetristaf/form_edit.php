<form method="POST" action="<?php echo module_url('dosimetristaf/save/' . $dosimetri->T04_ID_DOS_STAF); ?>" enctype="multipart/form-data">
    <div class="col-lg-12">
        <div class="card">
            <div class="px-4 py-3 border-bottom">
                <h5 class="card-title fw-semibold mb-0">KEMASKINI DATA DOSIMETRI</h5>
            </div>

            <!-- Tarikh -->
            <div class="mb-4 row align-items-center mt-3">
                <label for="tarikh" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="tarikh" name="tarikh"
                        value="<?= !empty($dosimetri->T04_TARIKH) ? date('Y-m-d', strtotime($dosimetri->T04_TARIKH)) : ''; ?>" required>
                </div>
            </div>

            <!-- Kategori Pengguna -->
            <div class="mb-4 row align-items-center">
                <label for="kategori_pengguna" class="form-label fw-semibold col-sm-3 col-form-label text-end">Kategori Pengguna</label>
                <div class="col-sm-9">
                    <select class="form-select" id="kategori_pengguna" name="kategori_pengguna" required>
                        <option value="">Sila Pilih</option>
                        <option value="Juru Xray" <?= $dosimetri->T04_KATEGORI_PENGGUNA == 'Juru Xray' ? 'selected' : '' ?>>Juru Xray</option>
                        <option value="Doktor" <?= $dosimetri->T04_KATEGORI_PENGGUNA == 'Doktor' ? 'selected' : '' ?>>Doktor</option>
                        <option value="Doktor Pergigian" <?= $dosimetri->T04_KATEGORI_PENGGUNA == 'Doktor Pergigian' ? 'selected' : '' ?>>Doktor Pergigian</option>
                        <option value="Pembantu Pergigian" <?= $dosimetri->T04_KATEGORI_PENGGUNA == 'Pembantu Pergigian' ? 'selected' : '' ?>>Pembantu Pergigian</option>
                    </select>
                </div>
            </div>

            <!-- Nama Pengguna -->
            <div class="mb-4 row align-items-center">
                <label for="nama_pengguna" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pengguna</label>
                <div class="col-sm-9">
                    <select class="form-select" id="nama_pengguna" name="nama_pengguna" required>
                        <option value="<?= htmlspecialchars($dosimetri->T04_NAMA_PENGGUNA) ?>">
                            <?= htmlspecialchars($dosimetri->T04_NAMA_PENGGUNA) ?>
                        </option>
                    </select>
                </div>
            </div>

            <!-- Dos Setara 1 -->
            <div class="mb-4 row align-items-center">
                <label for="dos_setara1" class="form-label fw-semibold col-sm-3 col-form-label text-end">Dos Setara 1</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control decimal-input" id="dos_setara1" name="dos_setara1"
                        value="<?= $dosimetri->T04_DOS_SETARA1 ?? '' ?>" pattern="^\d*\.?\d*$" title="Hanya nombor dan titik perpuluhan dibenarkan" required>
                </div>
            </div>

            <!-- Dos Setara 2 -->
            <div class="mb-4 row align-items-center">
                <label for="dos_setara2" class="form-label fw-semibold col-sm-3 col-form-label text-end">Dos Setara 2</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control decimal-input" id="dos_setara2" name="dos_setara2"
                        value="<?= $dosimetri->T04_DOS_SETARA2 ?? '' ?>" pattern="^\d*\.?\d*$" title="Hanya nombor dan titik perpuluhan dibenarkan" required>
                </div>
            </div>

            <!-- Buttons -->
            <div class="row mb-4">
                <div class="col-sm-3"></div>
                <div class="col-sm-9">
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary">Kemaskini</button>
                        <a href="<?php echo module_url('dosimetristaf/listdos_staff'); ?>" class="btn bg-danger-subtle text-danger">Batal</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Decimal validation
    document.querySelectorAll('.decimal-input').forEach(input => {
        input.addEventListener('change', function() {
            this.value = this.value.replace(',', '.');
            if (!/^\d*\.?\d*$/.test(this.value)) {
                alert('Format nombor tidak sah. Gunakan contoh seperti: 12.34');
                this.value = '';
                this.focus();
            }
        });
    });

    // Fetch staff names based on category
    const kategoriSelect = document.getElementById("kategori_pengguna");
    const namaPenggunaSelect = document.getElementById("nama_pengguna");

    kategoriSelect.addEventListener('change', function() {
        const kategori = this.value;
        namaPenggunaSelect.innerHTML = '<option value="">Sila Pilih</option>';

        if (!kategori) return;

        fetch(`<?= module_url('dosimetristaf/get_staff_by_category'); ?>?category=${encodeURIComponent(kategori)}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(staff => {
                    const option = document.createElement("option");
                    option.value = staff.name;
                    option.textContent = staff.name;
                    namaPenggunaSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error("Error fetching staff:", err);
                const option = document.createElement("option");
                option.value = "";
                option.textContent = "Ralat memuat data staf";
                namaPenggunaSelect.appendChild(option);
            });
    });
});
</script>
