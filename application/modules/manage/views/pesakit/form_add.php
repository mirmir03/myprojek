<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesakit Baharu</title>
    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .container-fluid {
            height: 100vh;
            overflow: hidden;
            padding: 20px;
        }

        .card {
            max-height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            flex-shrink: 0;
        }

        .card-body {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        /* Custom scrollbar for better UX */
        .card-body::-webkit-scrollbar {
            width: 8px;
        }

        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Enhanced form controls */
        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.975rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            height: auto;
            min-height: 38px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-control:hover, .form-select:hover {
            border-color: #86b7fe;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.975rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .bg-danger-subtle {
            background-color: #f8d7da !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        /* Modal enhancements */
        .modal-content {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header.bg-warning {
            background-color: #fff3cd !important;
            border-bottom: 1px solid #ffecb5;
        }

        .form-text {
            font-size: 0.875em;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        /* Button group styling */
        .d-flex.gap-3 {
            gap: 1rem !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }
            
            .card {
                max-height: calc(100vh - 20px);
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .col-form-label {
                text-align: left !important;
                margin-bottom: 0.5rem;
            }
            
            .mb-4.row.align-items-center {
                margin-bottom: 1rem !important;
            }
        }

        /* Ensure proper spacing */
        .row.align-items-center {
            margin-bottom: 0;
        }

        .row.align-items-center > .col-sm-3 {
            padding-right: 1rem;
        }

        .row.align-items-center > .col-sm-9 {
            padding-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <form method="POST" action="<?php echo module_url('pesakit/add'); ?>" enctype="multipart/form-data">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-semibold mb-0">
                            <?= isset($page_title) ? $page_title : 'DAFTAR PESAKIT BAHARU'; ?>
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Tarikh -->
                        <div class="mb-4 row align-items-center">
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
                                    <option value="">Sila Pilih</option>
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
                            <label for="no_rujukan" class="form-label fw-semibold col-sm-3 col-form-label text-end">No Pengenalan</label>
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
                                    <option value="">Sila Pilih</option>
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
                                <select class="form-select" id="sub_bhg" name="sub_bhg" disabled required>
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
            </div>
        </form>
    </div>

    <!-- Custom Popout Modal -->
    <div class="modal fade" id="rujukanModal" tabindex="-1" aria-labelledby="rujukanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="rujukanModalLabel">Format Tidak Sah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="rujukanModalMessage">
                    <!-- Message will be inserted here dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dateInput = document.getElementById("tarikh");
            const today = new Date();
            dateInput.value = today.toISOString().split('T')[0];

            const subOptions = {
                "Skull and Head": ["Skull (AP/PA and lateral views)", "Sinuses (paranasal sinuses)", "Facial Bones", "Mandible (jaw)", "Temporomandibular Joint (TMJ)"],
                "Spine": ["Cervical Spine", "Thoracic Spine", "Lumbar Spine", "Sacrum and Coccyx", "Full Spine"],
                "Chest": ["Lungs", "Heart", "Ribs", "Sternum", "Clavicle", "Diaphragm"],
                "Abdomen": ["KUB", "Gas patterns", "Foreign Body Localization"],
                "Upper Extremities": ["Shoulder", "Humerus", "Elbow", "Forearm", "Wrist", "Hand", "Fingers"],
                "Lower Extremities": ["Pelvis", "Hip", "Femur", "Knee", "Tibia and Fibula", "Ankle", "Foot", "Toes"]
            };

            document.getElementById('bhg_utama').addEventListener('change', function () {
                const subSelect = document.getElementById('sub_bhg');
                subSelect.innerHTML = '<option value="" disabled selected>Sila Pilih</option>';
                subSelect.disabled = !this.value;
                if (subOptions[this.value]) {
                    subOptions[this.value].forEach(opt => {
                        const option = document.createElement("option");
                        option.value = opt;
                        option.textContent = opt;
                        subSelect.appendChild(option);
                    });
                }
            });

            const kategoriSelect = document.getElementById("kategori");
            const noRujukanInput = document.getElementById("no_rujukan");
            const namaInput = document.getElementById("nama_pesakit");
            const jantinaSelect = document.getElementById("jantina");
            const formatHelp = document.getElementById("rujukan_format");

            const formatPatterns = {
                pelajar: /^\d{6}-\d{2}-\d{4}$/,
                staf: /^\d{6}-\d{2}-\d{4}$/,
                pesara: /^\d{6}-\d{2}-\d{4}$/,
                tanggungan: /^\d{6}-\d{2}-\d{4}$/,
                "warga luar": /^\d{6}-\d{2}-\d{4}$/
            };

            const formatMessages = {
                pelajar: "Format: 030408-03-0504 (IC Pelajar)",
                staf: "Format: 030408-03-0504 (IC Staf)",
                pesara: "Format: 030408-03-0504",
                tanggungan: "Format: 030408-03-0504",
                "warga luar": "Format: 030408-03-0504"
            };

            kategoriSelect.addEventListener("change", () => {
                const selected = kategoriSelect.value;
                formatHelp.textContent = formatMessages[selected] || "";
                namaInput.readOnly = selected === "pelajar" || selected === "staf";
                namaInput.value = "";
                noRujukanInput.value = "";
                jantinaSelect.value = "";
            });

            function validateFormat(kategori, value) {
                const pattern = formatPatterns[kategori];
                return pattern ? pattern.test(value) : false;
            }

            function getGenderFromIC(ic) {
                const lastDigit = parseInt(ic.replace(/-/g, '').slice(-1));
                return lastDigit % 2 === 0 ? "Perempuan" : "Lelaki";
            }

            function showPopoutMessage(message) {
                document.getElementById('rujukanModalMessage').textContent = message;
                var modal = new bootstrap.Modal(document.getElementById('rujukanModal'));
                modal.show();
            }

            // ENHANCED: Main blur event listener for no_rujukan input
            noRujukanInput.addEventListener("blur", function () {
                const kategori = kategoriSelect.value;
                const rujukan = this.value.trim();
                
                console.log('Processing:', { kategori, rujukan });
                
                // Skip if empty
                if (!rujukan) return;
                
                // Skip if kategori not selected
                if (!kategori) {
                    showPopoutMessage('Sila pilih kategori terlebih dahulu.');
                    this.value = "";
                    return;
                }
                
                // Validate format
                if (!validateFormat(kategori, rujukan)) {
                    showPopoutMessage(`Format No Rujukan tidak sah. Gunakan format: ${formatMessages[kategori]}`);
                    this.value = "";
                    this.focus();
                    return;
                }

                // Handle staff/student categories - fetch both name AND gender from database
                if (kategori === "pelajar" || kategori === "staf") {
                    console.log('Fetching staff/student data (name + gender)...');
                    
                    // Clear previous values
                    namaInput.value = "";
                    jantinaSelect.value = "";
                    
                    // Prepare form data
                    const formData = new FormData();
                    formData.append("no_rujukan", rujukan);
                    formData.append("kategori", kategori);

                    // Make AJAX request
                    fetch("<?php echo module_url('pesakit/get_patient_data'); ?>", {
                        method: "POST",
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        
                        return response.text(); // Get as text first
                    })
                    .then(text => {
                        console.log('Raw response:', text);
                        
                        // Try to parse JSON
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response: ' + text);
                        }
                        
                        console.log('Parsed data:', data);
                        
                        // Handle response
                        if (data.status === "success") {
                            // Set both name and gender from database
                            namaInput.value = data.data.nama || "";
                            jantinaSelect.value = data.data.jantina
        ? data.data.jantina.charAt(0).toUpperCase() + data.data.jantina.slice(1).toLowerCase()
        : "";

                            
                            console.log('✓ Data loaded successfully:', {
                                nama: data.data.nama,
                                jantina: data.data.jantina
                            });
                            
                            // Fallback: if no gender from DB, derive from IC
                            if (!data.data.jantina) {
                                jantinaSelect.value = getGenderFromIC(rujukan);
                                console.log('→ Used IC-derived gender as fallback:', getGenderFromIC(rujukan));
                            }
                        } else if (data.status === "not_found") {
                            showPopoutMessage(data.message || "No rujukan tidak ditemui dalam sistem.");
                            noRujukanInput.value = "";
                            console.log('✗ Data not found');
                        } else {
                            showPopoutMessage(data.message || "Ralat mencari data pesakit.");
                            noRujukanInput.value = "";
                            console.log('✗ Error response:', data);
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        showPopoutMessage("Ralat sambungan ke server. Sila cuba lagi.");
                        noRujukanInput.value = "";
                    });
                } else {
                    // For other categories (pesara, tanggungan, warga luar)
                    // Derive gender from IC number only
                    const derivedGender = getGenderFromIC(rujukan);
                    jantinaSelect.value = derivedGender;
                    console.log('✓ Set gender for other category:', derivedGender);
                }
            });

            // Enhanced test function
            window.testAjax = function() {
                console.log('Testing AJAX connection...');
                
                const formData = new FormData();
                formData.append("no_rujukan", "030425-04-0678");
                formData.append("kategori", "pelajar");

                fetch("<?php echo module_url('pesakit/test_ajax'); ?>", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('✓ AJAX Test Result:', data);
                })
                .catch(error => {
                    console.error('✗ AJAX Test Error:', error);
                });
            };

            // Enhanced data fetch test
            window.testDataFetch = function(testIC = "030425-04-0678", testKategori = "pelajar") {
                console.log(`Testing data fetch for: ${testIC} (${testKategori})`);
                
                const formData = new FormData();
                formData.append("no_rujukan", testIC);
                formData.append("kategori", testKategori);

                fetch("<?php echo module_url('pesakit/get_patient_data'); ?>", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(text => {
                    console.log('Raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        console.log('✓ Parsed data:', data);
                        
                        if (data.status === 'success') {
                            console.log('→ Name:', data.data.nama);
                            console.log('→ Gender:', data.data.jantina);
                        }
                    } catch (e) {
                        console.error('✗ JSON parse error:', e);
                    }
                })
                .catch(error => {
                    console.error('✗ Fetch error:', error);
                });
            };

            // Test function for non-staff categories
            window.testOtherCategory = function() {
                const testIC = "030425-04-0678";
                const testKategori = "pesara";
                
                console.log(`Testing other category: ${testIC} (${testKategori})`);
                
                const formData = new FormData();
                formData.append("no_rujukan", testIC);
                formData.append("kategori", testKategori);

                fetch("<?php echo module_url('pesakit/get_patient_data'); ?>", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('✓ Other category result:', data);
                })
                .catch(error => {
                    console.error('✗ Other category error:', error);
                });
            };
        });
    </script>
</body>
</html>