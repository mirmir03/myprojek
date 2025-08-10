<form method="POST" action="<?php echo module_url("notifikasi/update/".$data->T06_ID_NOTIFIKASI)?>" enctype="multipart/form-data">
  <div class="col-lg-12">
    <div class="card">
      <div class="px-4 py-3 border-bottom">
        <h5 class="card-title fw-semibold mb-0">EDIT NOTIFIKASI PESANAN</h5>
      </div>
      
      <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger mx-4 mt-3">
          <?php echo $this->session->flashdata('error'); ?>
      </div>
      <?php endif; ?>
      
      <!-- Nama Pembekal -->
      <div class="mb-4 row align-items-center mt-3">
        <label for="nama_pembekal" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pembekal</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="nama_pembekal" name="nama_pembekal" 
                 value="<?php echo htmlspecialchars($data->T06_NAMA_PEMBEKAL); ?>" 
                 required>
        </div>
      </div>

      <!-- Nombor Telefon -->
      <div class="mb-4 row align-items-center">
        <label for="nombor_telefon" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nombor Telefon</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="nombor_telefon" name="nombor_telefon" 
                 value="<?php echo htmlspecialchars($data->T06_NOMBOR_TELEFON); ?>" 
                 required>
        </div>
      </div>

      <!-- Nombor Pesanan -->
      <div class="mb-4 row align-items-center">
        <label for="nombor_pesanan" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nombor Pesanan</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="nombor_pesanan" name="nombor_pesanan" 
                 value="<?php echo htmlspecialchars($data->T06_NOMBOR_PESANAN); ?>" 
                 required>
        </div>
      </div>

      <!-- Tarikh Pesanan -->
      <div class="mb-4 row align-items-center">
        <label for="tarikh_pesanan" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tarikh Pesanan</label>
        <div class="col-sm-9">
          <input type="date" class="form-control" id="tarikh_pesanan" name="tarikh_pesanan" 
                 value="<?php echo date('Y-m-d', strtotime($data->T06_TARIKH_PESANAN)); ?>" 
                 required>
        </div>
      </div>

      <!-- Tamat Pesanan -->
      <div class="mb-4 row align-items-center">
        <label for="tamat_pesanan" class="form-label fw-semibold col-sm-3 col-form-label text-end">Tamat Pesanan</label>
        <div class="col-sm-9">
          <input type="date" class="form-control" id="tamat_pesanan" name="tamat_pesanan" 
                 value="<?php echo date('Y-m-d', strtotime($data->T06_TAMAT_PESANAN)); ?>" 
                 required>
        </div>
      </div>

      <!-- Jumlah Harga -->
      <div class="mb-4 row align-items-center">
        <label for="jumlah_harga" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jumlah Harga (RM)</label>
        <div class="col-sm-9">
          <input type="number" step="0.01" class="form-control" id="jumlah_harga" name="jumlah_harga" 
                 value="<?php echo htmlspecialchars($data->T06_JUMLAH_HARGA); ?>" 
                 required>
        </div>
      </div>

      <!-- Fail PDF -->
      <div class="mb-4 row align-items-center">
        <label for="pdf_file" class="form-label fw-semibold col-sm-3 col-form-label text-end">Fail PDF</label>
        <div class="col-sm-9">
          <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf">
          <?php if (!empty($data->T06_PDF_FILE)): ?>
            <small class="text-muted mt-2 d-block">
              Fail semasa: 
              <a href="<?php echo base_url('uploads/pdf/' . $data->T06_PDF_FILE); ?>" target="_blank">
                <?php echo $data->T06_PDF_FILE; ?>
              </a>
            </small>
          <?php endif; ?>
        </div>
      </div>

      <!-- Buttons -->
      <div class="row mb-4">
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
          <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn bg-danger-subtle text-danger" onclick="window.location='<?php echo module_url('notifikasi')?>'">Batal</button>
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
        const tarikhPesanan = document.getElementById("tarikh_pesanan").value;
        const tamatPesanan = document.getElementById("tamat_pesanan").value;
        
        // Validate date range
        if (new Date(tamatPesanan) < new Date(tarikhPesanan)) {
            event.preventDefault();
            alert("Tarikh Tamat mestilah selepas Tarikh Pesanan.");
            document.getElementById("tamat_pesanan").focus();
            return false;
        }
        
        // Calculate difference in days
        const diffTime = Math.abs(new Date(tamatPesanan) - new Date(tarikhPesanan));
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
        
        if (diffDays < 3) {
            event.preventDefault();
            alert("Tarikh Tamat mestilah sekurang-kurangnya 3 hari selepas Tarikh Pesanan.");
            document.getElementById("tamat_pesanan").focus();
            return false;
        }
    });
});
</script>