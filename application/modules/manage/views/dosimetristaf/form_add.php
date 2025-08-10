<form method="POST" action="<?php echo module_url('dosimetristaf/add'); ?>" enctype="multipart/form-data">
    <div class="col-lg-12">
        <div class="card">
            <div class="px-4 py-3 border-bottom">
                <h5 class="card-title fw-semibold mb-0">TAMBAH DATA DOSIMETRI BAHARU</h5>
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

            <!-- Kategori Pengguna -->
            <div class="mb-4 row align-items-center">
                <label for="kategori_pengguna" class="form-label fw-semibold col-sm-3 col-form-label text-end">Kategori Pengguna</label>
                <div class="col-sm-9">
                    <select class="form-select" id="kategori_pengguna" name="kategori_pengguna" required>
                        <option value="">Sila Pilih</option>
                        <option value="Juru Xray">Juru Xray</option>
                        <option value="Doktor">Doktor</option>
                        <option value="Doktor Pergigian">Doktor Pergigian</option>
                        <option value="Pembantu Pergigian">Pembantu Pergigian</option>
                    </select>
                </div>
            </div>

            <!-- Nama Pengguna -->
            <div class="mb-4 row align-items-center">
                <label for="nama_pengguna" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pengguna</label>
                <div class="col-sm-9">
                    <select class="form-select" id="nama_pengguna" name="nama_pengguna" required>
                        <option value="">Sila Pilih Kategori Dahulu</option>
                    </select>
                </div>
            </div>

            <!-- Dos Setara 1 -->
            <div class="mb-4 row align-items-center">
                <label for="dos_setara1" class="form-label fw-semibold col-sm-3 col-form-label text-end">Dos Setara 1</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control decimal-input" id="dos_setara1" name="dos_setara1" 
                           pattern="^\d*\.?\d*$" title="Hanya nombor dan titik perpuluhan dibenarkan" required>
                </div>
            </div>

            <!-- Dos Setara 2 -->
            <div class="mb-4 row align-items-center">
                <label for="dos_setara2" class="form-label fw-semibold col-sm-3 col-form-label text-end">Dos Setara 2</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control decimal-input" id="dos_setara2" name="dos_setara2"
                           pattern="^\d*\.?\d*$" title="Hanya nombor dan titik perpuluhan dibenarkan" required>
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
    // Set today's date
    const dateInput = document.getElementById("tarikh");
    dateInput.value = new Date().toISOString().split('T')[0];

    // Decimal input validation
    document.querySelectorAll('.decimal-input').forEach(input => {
        input.addEventListener('change', function() {
            // Replace comma with dot if exists
            this.value = this.value.replace(',', '.');
            
            // Validate decimal format
            if (!/^\d*\.?\d*$/.test(this.value)) {
                alert('Format nombor tidak sah. Sila gunakan format seperti: 12.34');
                this.value = '';
                this.focus();
            }
        });
    });

    // Fetch staff names based on category selection
    const kategoriSelect = document.getElementById("kategori_pengguna");
    const namaPenggunaSelect = document.getElementById("nama_pengguna");
    
    kategoriSelect.addEventListener('change', function() {
        const selectedKategori = this.value;
        
        // Clear existing options
        namaPenggunaSelect.innerHTML = '<option value="">Sila Pilih</option>';
        
        if (!selectedKategori) return;
        
        // Fetch staff names via AJAX
        fetch(`<?php echo module_url('dosimetristaf/get_staff_by_category'); ?>?category=${encodeURIComponent(selectedKategori)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(staff => {
                        const option = document.createElement('option');
                        option.value = staff.name;
                        option.textContent = staff.name;
                        namaPenggunaSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Tiada staf dalam kategori ini';
                    namaPenggunaSelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error fetching staff data:', error);
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Ralat ketika memuatkan data staf';
                namaPenggunaSelect.appendChild(option);
            });
    });

    // Form validation
    document.querySelector("form").addEventListener("submit", function(event) {
        // Basic validation
        const requiredFields = ['tarikh', 'nama_pengguna', 'kategori_pengguna', 'dos_setara1', 'dos_setara2'];
        for (const field of requiredFields) {
            if (!document.getElementById(field).value) {
                alert(`Sila isi ruangan ${field.replace('_', ' ')}`);
                event.preventDefault();
                return false;
            }
        }

        // Decimal field validation
        const decimalFields = ['dos_setara1', 'dos_setara2'];
        for (const field of decimalFields) {
            const value = document.getElementById(field).value;
            if (value && !/^\d*\.?\d*$/.test(value)) {
                alert(`Format nombor tidak sah untuk ${field.replace('_', ' ')}`);
                event.preventDefault();
                return false;
            }
        }
    });
});

document.querySelector("form").addEventListener("submit", async function(event) {
    event.preventDefault();
    
    // Basic validation
    const requiredFields = ['tarikh', 'nama_pengguna', 'kategori_pengguna', 'dos_setara1', 'dos_setara2'];
    for (const field of requiredFields) {
        if (!document.getElementById(field).value) {
            alert(`Sila isi ruangan ${field.replace('_', ' ')}`);
            return false;
        }
    }

    // Decimal validation
    const decimalFields = ['dos_setara1', 'dos_setara2'];
    for (const field of decimalFields) {
        const value = document.getElementById(field).value;
        if (value && !/^\d*\.?\d*$/.test(value)) {
            alert(`Format nombor tidak sah untuk ${field.replace('_', ' ')}`);
            return false;
        }
    }
    
    // Check for existing data for this staff
    const staffName = document.getElementById("nama_pengguna").value;
    const date = new Date(document.getElementById("tarikh").value);
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    try {
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader"></i> Menyemak...';
        
        const response = await fetch(`<?php echo module_url('dosimetristaf/check_existing'); ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `staff_name=${encodeURIComponent(staffName)}&month=${month}&year=${year}`
        });
        
        const result = await response.json();
        
        // Restore button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan';
        
        if (result.exists) {
            alert('Anda sudah merekodkan dosimetri untuk bulan ini. Sila kemaskini rekod sedia ada.');
            return false;
        }
        
        // If no existing data, submit the form
        this.submit();
        
    } catch (error) {
        console.error('Error checking existing data:', error);
        alert('Ralat ketika menyemak data sedia ada. Sila cuba lagi.');
        // Restore button state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Simpan';
    }
});
</script>