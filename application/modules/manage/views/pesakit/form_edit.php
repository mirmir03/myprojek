<form method="POST" action="<?php echo module_url("pesakit/save/".$pesakit->T01_ID_PESAKIT)?>" enctype="multipart/form-data">
  <div class="col-lg-12">
    <div class="card">
      <div class="px-4 py-3 border-bottom">
        <h5 class="card-title fw-semibold mb-0">EDIT PESAKIT</h5>
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
          <input type="date" class="form-control" id="tarikh" name="tarikh" value="<?php echo isset($pesakit->T01_TARIKH) ? $pesakit->T01_TARIKH : date('Y-m-d'); ?>" readonly>
        </div>
      </div>

      <!-- Nama Pesakit -->
      <div class="mb-4 row align-items-center">
        <label for="nama_pesakit" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pesakit</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="nama_pesakit" name="nama_pesakit" value="<?php echo $pesakit->T01_NAMA_PESAKIT?>" required>
        </div>
      </div>

      <!-- No Rujukan -->
      <div class="mb-4 row align-items-center">
        <label for="no_rujukan" class="form-label fw-semibold col-sm-3 col-form-label text-end">No Rujukan</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="no_rujukan" name="no_rujukan" value="<?php echo $pesakit->T01_NO_RUJUKAN?>" required>
          <small id="rujukan_format" class="form-text text-muted"></small>
        </div>
      </div>

      <!-- Jantina -->
      <div class="mb-4 row align-items-center">
        <label for="jantina" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jantina</label>
        <div class="col-sm-9">
          <select class="form-select" id="jantina" name="jantina" required>
            <option value="" <?php echo empty($pesakit->T01_JANTINA) ? 'selected' : ''; ?>>Sila Pilih</option>
            <option value="Lelaki" <?php echo $pesakit->T01_JANTINA === 'Lelaki' ? 'selected' : ''; ?>>Lelaki</option>
            <option value="Perempuan" <?php echo $pesakit->T01_JANTINA === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
          </select>
        </div>
      </div>

      <!-- Kategori -->
      <div class="mb-4 row align-items-center">
        <label for="kategori" class="form-label fw-semibold col-sm-3 col-form-label text-end">Kategori</label>
        <div class="col-sm-9">
          <select class="form-select" id="kategori" name="kategori" required>
            <option value="" disabled <?php echo empty($pesakit->T01_KATEGORI) ? 'selected' : ''; ?>>Sila Pilih</option>
            <option value="pelajar" <?php echo strtolower($pesakit->T01_KATEGORI) === 'pelajar' ? 'selected' : ''; ?>>Pelajar</option>
            <option value="staf" <?php echo (strtolower($pesakit->T01_KATEGORI) === 'staf' || strtolower($pesakit->T01_KATEGORI) === 'staff') ? 'selected' : ''; ?>>Staf UMT</option>
            <option value="pesara" <?php echo strtolower($pesakit->T01_KATEGORI) === 'pesara' ? 'selected' : ''; ?>>Pesara</option>
            <option value="tanggungan" <?php echo strtolower($pesakit->T01_KATEGORI) === 'tanggungan' ? 'selected' : ''; ?>>Tanggungan Staf</option>
            <option value="warga luar" <?php echo (strtolower($pesakit->T01_KATEGORI) === 'warga luar' || strtolower($pesakit->T01_KATEGORI) === 'komuniti') ? 'selected' : ''; ?>>Warga Luar</option>
          </select>
        </div>
      </div>

      <!-- Pilihan Utama -->
      <div class="mb-4 row align-items-center">
        <label for="bhg_utama" class="form-label fw-semibold col-sm-3 col-form-label text-end">Pilihan Utama</label>
        <div class="col-sm-9">
          <select class="form-select" id="bhg_utama" name="bhg_utama" required>
            <option value="" disabled <?php echo empty($pesakit->T01_BAHAGIAN_UTAMA) ? 'selected' : ''; ?>>Sila Pilih</option>
            <option value="Skull and Head" <?php echo $pesakit->T01_BAHAGIAN_UTAMA === 'Skull and Head' ? 'selected' : ''; ?>>Skull and Head</option>
            <option value="Spine" <?php echo $pesakit->T01_BAHAGIAN_UTAMA === 'Spine' ? 'selected' : ''; ?>>Spine</option>
            <option value="Chest" <?php echo $pesakit->T01_BAHAGIAN_UTAMA === 'Chest' ? 'selected' : ''; ?>>Chest</option>
            <option value="Abdomen" <?php echo $pesakit->T01_BAHAGIAN_UTAMA === 'Abdomen' ? 'selected' : ''; ?>>Abdomen</option>
            <option value="Upper Extremities" <?php echo $pesakit->T01_BAHAGIAN_UTAMA === 'Upper Extremities' ? 'selected' : ''; ?>>Upper Extremities</option>
            <option value="Lower Extremities" <?php echo $pesakit->T01_BAHAGIAN_UTAMA === 'Lower Extremities' ? 'selected' : ''; ?>>Lower Extremities</option>
          </select>
        </div>
      </div>

      <!-- Pilihan Sub -->
      <div class="mb-4 row align-items-center">
        <label for="sub_bhg" class="form-label fw-semibold col-sm-3 col-form-label text-end">Pilihan Sub</label>
        <div class="col-sm-9">
          <select class="form-select" id="sub_bhg" name="sub_bhg" required>
            <option value="" disabled <?php echo empty($pesakit->T01_SUB_BAHAGIAN) ? 'selected' : ''; ?>>Pilih Pilihan Utama Dulu</option>
            <?php if (!empty($pesakit->T01_SUB_BAHAGIAN)): ?>
              <option value="<?php echo $pesakit->T01_SUB_BAHAGIAN; ?>" selected><?php echo $pesakit->T01_SUB_BAHAGIAN; ?></option>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <!-- Buttons -->
      <div class="row mb-4">
        <div class="col-sm-3"></div>
        <div class="col-sm-9">
          <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn bg-danger-subtle text-danger" onclick="window.location='<?php echo module_url('pesakit/listpesakit')?>'">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const subOptions = {
    "Skull and Head": ["Skull (AP/PA and lateral views)", "Sinuses (paranasal sinuses)", "Facial Bones", "Mandible (jaw)", "Temporomandibular Joint (TMJ)"],
    "Spine": ["Cervical Spine (neck)", "Thoracic Spine (mid-back)", "Lumbar Spine (lower back)", "Sacrum and Coccyx", "Full Spine (scoliosis studies)"],
    "Chest": ["Lungs", "Heart", "Ribs", "Sternum", "Clavicle (collarbone)", "Diaphragm"],
    "Abdomen": ["Kidneys, Ureters, and Bladder (KUB)", "Gas patterns (for bowel obstruction or perforation)", "Foreign Body Localization"],
    "Upper Extremities": ["Shoulder", "Humerus (upper arm)", "Elbow", "Forearm (radius and ulna)", "Wrist", "Hand", "Fingers"],
    "Lower Extremities": ["Pelvis", "Hip", "Femur (thigh bone)", "Knee", "Tibia and Fibula (lower leg)", "Ankle", "Foot", "Toes"]
  };

  // Initialize sub options when page loads if main option is already selected
  const mainOption = document.getElementById('bhg_utama').value;
  if (mainOption) {
    const subOptionSelect = document.getElementById('sub_bhg');
    const currentSubOption = subOptionSelect.querySelector('option:checked').value;

    // Clear and populate the dropdown
    subOptionSelect.innerHTML = '<option value="" disabled>Sila Pilih</option>';
    subOptions[mainOption].forEach(subOption => {
      const option = document.createElement('option');
      option.value = subOption;
      option.textContent = subOption;
      if (subOption === currentSubOption) {
        option.selected = true;
      }
      subOptionSelect.appendChild(option);
    });
    subOptionSelect.disabled = false;
  }

  // Event listener for main option change
  document.getElementById('bhg_utama').addEventListener('change', function() {
    const selectedOption = this.value;
    const subOptionSelect = document.getElementById('sub_bhg');
    subOptionSelect.innerHTML = ''; // Clear previous options
    if (selectedOption) {
      subOptionSelect.disabled = false;
      subOptionSelect.innerHTML = '<option value="" selected disabled>Sila Pilih</option>';
      subOptions[selectedOption].forEach(subOption => {
        const option = document.createElement('option');
        option.value = subOption;
        option.textContent = subOption;
        subOptionSelect.appendChild(option);
      });
    } else {
      subOptionSelect.disabled = true;
      subOptionSelect.innerHTML = '<option value="" selected disabled>Pilih Pilihan Utama Dulu</option>';
    }
  });

  // Category and reference number handling
  const kategoriSelect = document.getElementById("kategori");
  const noRujukanInput = document.getElementById("no_rujukan");
  const namaInput = document.getElementById("nama_pesakit");
  const jantinaSelect = document.getElementById("jantina");
  const formatHelp = document.getElementById("rujukan_format");
  
  // Set format help based on current category
  function updateFormatHelp() {
    const selectedKategori = kategoriSelect.value;
    
    switch(selectedKategori) {
      case "pelajar":
        formatHelp.textContent = "Format: s12345 (Bermula dengan 's' diikuti dengan nombor matrik)";
        break;
      case "staf":
        formatHelp.textContent = "Format: w1234 (Bermula dengan 'w' diikuti dengan nombor pekerja)";
        break;
      case "pesara":
      case "tanggungan":
      case "warga luar":
        formatHelp.textContent = "Format: 920906-03-0408 (Nombor kad pengenalan)";
        break;
      default:
        formatHelp.textContent = "";
    }
  }
  
  // Run on page load
  updateFormatHelp();
  
  // Update format help when category changes
  kategoriSelect.addEventListener("change", function() {
    updateFormatHelp();
  });
  
  // Function to validate no_rujukan format based on category
  function validateRujukanFormat(kategori, rujukan) {
    switch(kategori) {
      case "pelajar":
        return /^s\d+$/i.test(rujukan);
      case "staf":
        return /^w\d+$/i.test(rujukan);
      case "pesara":
      case "tanggungan":
      case "warga luar":
        return /^\d{6}[-]\d{2}[-]\d{4}$/.test(rujukan);
      default:
        return false;
    }
  }
  
  // Function to determine gender from IC number
  function determineGenderFromIC(icNumber) {
    if (!icNumber || icNumber.length < 12) return "";
    
    // Extract the relevant digits (positions 11-12 in format xxxxxx-xx-xxxx)
    const genderDigit = icNumber.split("-")[1];
    
    if (!genderDigit || genderDigit.length !== 2) return "";
    
    const numValue = parseInt(genderDigit, 10);
    
    // Odd number means female, even means male
    if (numValue % 2 === 1) {
      return "Perempuan"; // Perempuan (Female) - Odd numbers
    } else {
      return "Lelaki"; // Lelaki (Male) - Even numbers
    }
  }
  
  // Check rujukan format when input loses focus
  noRujukanInput.addEventListener("blur", function() {
    const selectedKategori = kategoriSelect.value;
    const rujukan = this.value.trim();
    
    if (!rujukan || !selectedKategori) {
      return;
    }
    
    // Validate format based on category
    if (!validateRujukanFormat(selectedKategori, rujukan)) {
      alert("Format No Rujukan tidak sah. Sila ikut format yang ditetapkan.");
      setTimeout(() => {
        this.focus();
        this.select();
      }, 100);
      return;
    }
    
    // For IC format, determine gender
    if (selectedKategori === "pesara" || selectedKategori === "tanggungan" || selectedKategori === "warga luar") {
      const gender = determineGenderFromIC(rujukan);
      if (gender) {
        jantinaSelect.value = gender;
      }
    }
  });
});
</script>


