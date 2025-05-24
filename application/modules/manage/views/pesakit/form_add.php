<form method="POST" action="<?php echo module_url('pesakit/add'); ?>" enctype="multipart/form-data">
    <div class="col-lg-12">
        <div class="card">
            <div class="px-4 py-3 border-bottom">
                <h5 class="card-title fw-semibold mb-0">DAFTAR PESAKIT BAHARU</h5>
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
                    <input type="date" class="form-control" id="tarikh" name="tarikh" required readonly>
                </div>
            </div>

            <!-- Kategori -->
            <div class="mb-4 row align-items-center">
                <label for="kategori" class="form-label fw-semibold col-sm-3 col-form-label text-end">Kategori</label>
                <div class="col-sm-9">
                    <select class="form-select" id="kategori" name="kategori" required>
                        <option value="" selected>Sila Pilih</option>
                        <option value="pelajar">Pelajar</option>
                        <option value="staf">Staf UMT</option>
                        <option value="pesara">Pesara</option>
                        <option value="tanggungan">Tanggungan Staf</option>
                        <option value="warga luar">Warga Luar</option>
                    </select>
                </div>
            </div>

            <!-- No Rujukan -->
            <div class="mb-4 row align-items-center">
                <label for="no_rujukan" class="form-label fw-semibold col-sm-3 col-form-label text-end">No Rujukan</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="no_rujukan" name="no_rujukan" required>
                    <small id="rujukan_format" class="form-text text-muted"></small>
                </div>
            </div>

            <!-- Nama Pesakit -->
            <div class="mb-4 row align-items-center">
                <label for="nama_pesakit" class="form-label fw-semibold col-sm-3 col-form-label text-end">Nama Pesakit</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="nama_pesakit" name="nama_pesakit" required>
                </div>
            </div>

            <!-- Jantina -->
            <div class="mb-4 row align-items-center">
                <label for="jantina" class="form-label fw-semibold col-sm-3 col-form-label text-end">Jantina</label>
                <div class="col-sm-9">
                    <select class="form-select" id="jantina" name="jantina" required>
                        <option value="" selected>Sila Pilih</option>
                        <option value="Lelaki">Lelaki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Pilihan Utama -->
            <div class="mb-4 row align-items-center">
                <label for="bhg_utama" class="form-label fw-semibold col-sm-3 col-form-label text-end">Pilihan Utama</label>
                <div class="col-sm-9">
                    <select class="form-select" id="bhg_utama" name="bhg_utama" required>
                        <option value="" selected disabled>Sila Pilih</option>
                        <option value="Skull and Head">Skull and Head</option>
                        <option value="Spine">Spine</option>
                        <option value="Chest">Chest</option>
                        <option value="Abdomen">Abdomen</option>
                        <option value="Upper Extremities">Upper Extremities</option>
                        <option value="Lower Extremities">Lower Extremities</option>
                    </select>
                </div>
            </div>

            <!-- Pilihan Sub -->
            <div class="mb-4 row align-items-center">
                <label for="sub_bhg" class="form-label fw-semibold col-sm-3 col-form-label text-end">Pilihan Sub</label>
                <div class="col-sm-9">
                    <select class="form-select" id="sub_bhg" name="sub_bhg" required disabled>
                        <option value="" selected disabled>Pilih Pilihan Utama Dulu</option>
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
   
    // Set the initial value to today
    dateInput.value = todayStr;

    const subOptions = {
        "Skull and Head": ["Skull (AP/PA and lateral views)", "Sinuses (paranasal sinuses)", "Facial Bones", "Mandible (jaw)", "Temporomandibular Joint (TMJ)"],
        "Spine": ["Cervical Spine (neck)", "Thoracic Spine (mid-back)", "Lumbar Spine (lower back)", "Sacrum and Coccyx", "Full Spine (scoliosis studies)"],
        "Chest": ["Lungs", "Heart", "Ribs", "Sternum", "Clavicle (collarbone)", "Diaphragm"],
        "Abdomen": ["Kidneys, Ureters, and Bladder (KUB)", "Gas patterns (for bowel obstruction or perforation)", "Foreign Body Localization"],
        "Upper Extremities": ["Shoulder", "Humerus (upper arm)", "Elbow", "Forearm (radius and ulna)", "Wrist", "Hand", "Fingers"],
        "Lower Extremities": ["Pelvis", "Hip", "Femur (thigh bone)", "Knee", "Tibia and Fibula (lower leg)", "Ankle", "Foot", "Toes"]
    };

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
    
    // Track validation state to prevent loops
    let isValidating = false;
    
    // Format patterns for validation
    const formatPatterns = {
        "pelajar": /^s\d+$/i,
        "staf": /^w\d+$/i,
        "pesara": /^\d{6}[-]\d{2}[-]\d{4}$/,
        "tanggungan": /^\d{6}[-]\d{2}[-]\d{4}$/,
        "warga luar": /^\d{6}[-]\d{2}[-]\d{4}$/
    };
    
    // Format messages for user guidance
    const formatMessages = {
        "pelajar": "Format Nom Matrik: s12345",
        "staf": "Format Id Staff: w1234",
        "pesara": "Format: 920906-03-0408 (Nombor kad pengenalan)",
        "tanggungan": "Format: 920906-03-0408 (Nombor kad pengenalan)",
        "warga luar": "Format: 920906-03-0408 (Nombor kad pengenalan)"
    };
    
    kategoriSelect.addEventListener("change", function() {
        const selectedKategori = this.value;
        
        // Clear previous values
        noRujukanInput.value = "";
        namaInput.value = "";
        jantinaSelect.value = "";
        
        // Set format help text based on selected category
        formatHelp.textContent = selectedKategori ? formatMessages[selectedKategori] : "";
        
        // If category is manual entry, enable the name field
        if (selectedKategori === "pesara" || selectedKategori === "tanggungan" || selectedKategori === "warga luar") {
            namaInput.readOnly = false;
        } else {
            namaInput.readOnly = true;
        }
        
        // Reset validation state when category changes
        isValidating = false;
        
        // Focus the rujukan field after category selection
        setTimeout(() => {
            noRujukanInput.focus();
        }, 100);
    });
    
    // Function to validate no_rujukan format based on category
    function validateRujukanFormat(kategori, rujukan) {
        if (!kategori || !rujukan) return false;
        
        const pattern = formatPatterns[kategori];
        return pattern ? pattern.test(rujukan) : false;
    }
    
    // Function to determine gender from IC number
    function determineGenderFromIC(icNumber) {
        if (!icNumber || icNumber.length < 12) return "";
        
        // Extract the last digit for gender determination
        const lastDigit = parseInt(icNumber.charAt(icNumber.length - 1), 10);
        
        // Even number means female, odd means male
        if (lastDigit % 2 === 0) {
            return "Perempuan"; // Perempuan (Female) - Even numbers
        } else {
            return "Lelaki"; // Lelaki (Male) - Odd numbers
        }
    }
    
    // Add event listener for kategori focus to reset validation state
    kategoriSelect.addEventListener("focus", function() {
        isValidating = false;
    });
    
    // Add event listener for no_rujukan focus to reset validation state
    noRujukanInput.addEventListener("focus", function() {
        isValidating = false;
    });
    
    // Handle input changes to provide immediate feedback
    noRujukanInput.addEventListener("input", function() {
        // Reset validation state on new input
        isValidating = false;
    });
    
    // Auto-populate name field on no_rujukan change for students and staff
    noRujukanInput.addEventListener("blur", function() {
        // Prevent validation loop
        if (isValidating) return;
        
        const selectedKategori = kategoriSelect.value;
        const rujukan = this.value.trim();
        
        if (!rujukan || !selectedKategori) {
            return;
        }
        
        // Set validating flag to prevent loops
        isValidating = true;
        
        // Validate format based on category
        if (!validateRujukanFormat(selectedKategori, rujukan)) {
            // More specific error message based on potential wrong format detection
            let errorMessage = `Format No Rujukan tidak sah untuk kategori ${selectedKategori}.`;
            
            // Check if user entered IC format for student/staff or vice versa
            if ((selectedKategori === "pelajar" || selectedKategori === "staf") && 
                /^\d{6}[-]\d{2}[-]\d{4}$/.test(rujukan)) {
                errorMessage = `Anda memasukkan nombor kad pengenalan. Untuk kategori ${selectedKategori}, sila gunakan format ${formatMessages[selectedKategori]}.`;
            } else if ((selectedKategori === "pesara" || selectedKategori === "tanggungan" || selectedKategori === "warga luar") &&
                      (/^s\d+$/i.test(rujukan) || /^w\d+$/i.test(rujukan))) {
                errorMessage = `Anda memasukkan format pelajar/staf. Untuk kategori ${selectedKategori}, sila gunakan format nombor kad pengenalan.`;
            }
            
            // Show alert with specific message
            alert(errorMessage);
            
            // Reset and focus the field
            this.value = "";
            
            // Reset validation state
            isValidating = false;
            
            // Focus back on the input field after alert is closed
            setTimeout(() => {
                this.focus();
            }, 100);
            
            return;
        }
        
        // For IC format (pesara, tanggungan, warga_luar), determine gender
        if (selectedKategori === "pesara" || selectedKategori === "tanggungan" || selectedKategori === "warga luar") {
            const gender = determineGenderFromIC(rujukan);
            if (gender) {
                jantinaSelect.value = gender;
            }
            
            // Reset validation state after successful validation
            isValidating = false;
            
            // Move focus to nama field
            setTimeout(() => {
                namaInput.focus();
            }, 100);
            
            return; // No need to fetch from database for these categories
        }
        
        // Fetch patient data from the database for students and staff
        const formData = new FormData();
        formData.append("no_rujukan", rujukan);
        formData.append("kategori", selectedKategori);
        
        fetch("<?php echo module_url('pesakit/get_patient_data'); ?>", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                namaInput.value = data.data.nama;
                // Set gender if available in the response
                if (data.data.jantina) {
                    jantinaSelect.value = data.data.jantina;
                }
                
                // Move focus to next field
                document.getElementById("bhg_utama").focus();
            } else {
                alert("No rujukan tidak ditemui dalam sistem. Sila semak semula.");
                this.value = "";
                this.focus();
                namaInput.value = "";
            }
            
            // Reset validation state after API response
            isValidating = false;
        })
        .catch(error => {
            console.error("Error fetching patient data:", error);
            alert("Ralat semasa mencari maklumat pesakit. Sila cuba lagi.");
            
            // Reset validation state after error
            isValidating = false;
        });
    });
    
    // Add form submit handler to perform final validation
    document.querySelector("form").addEventListener("submit", function(event) {
        const selectedKategori = kategoriSelect.value;
        const rujukan = noRujukanInput.value.trim();
        
        if (!validateRujukanFormat(selectedKategori, rujukan)) {
            event.preventDefault();
            alert(`Format No Rujukan tidak sah untuk kategori ${selectedKategori}. ${formatMessages[selectedKategori]}`);
            noRujukanInput.focus();
        }
    });
});
</script>



