<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peringatan Baharu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/2.44.0/tabler-icons.min.css" rel="stylesheet">
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .pdf-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
        }
        .pdf-info-card {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
        }
        .pdf-info-card.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
        }
        .pdf-info-card.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .file-drop-zone {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-drop-zone:hover,
        .file-drop-zone.dragover {
            border-color: #0d6efd;
            background-color: #f8f9ff;
        }
        .file-info {
            display: none;
            margin-top: 1rem;
            padding: 0.75rem;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 0.375rem;
        }
        .loading-spinner {
            display: none;
        }
        .success-animation {
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .data-summary {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        .save-actions {
            display: none;
            margin-top: 1rem;
        }
        .parsing-progress {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 0.375rem;
        }
        .hidden-file-input {
            position: absolute !important;
            opacity: 0 !important;
            width: 0.1px !important;
            height: 0.1px !important;
            overflow: hidden !important;
            z-index: -1 !important;
        }
        .main-upload-area {
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-bell-plus me-2"></i>
                            TAMBAH PERINGATAN BAHARU - UPLOAD PDF
                        </h5>
                    </div>
                    
                    <div class="card-body">
                        <!-- Error Display -->
                        <div class="alert alert-danger d-none" id="errorAlert">
                            <ul class="mb-0" id="errorList"></ul>
                        </div>
                        
                        <!-- Success Display -->
                        <div class="alert alert-success d-none" id="successAlert">
                            <span id="successMessage"></span>
                        </div>

                        <!-- Main Form -->
                        <form id="reminderForm" method="post" action="<?php echo current_url(); ?>/../add" enctype="multipart/form-data">
                            <!-- CSRF Token -->
                            <?php if(isset($csrf_token)): ?>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrfToken">
                            <?php else: ?>
                                <input type="hidden" name="csrf_token" value="sample_csrf_token" id="csrfToken">
                            <?php endif; ?>
                            
                            <!-- Hidden File Input -->
                            <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" class="hidden-file-input">
                            
                            <!-- Hidden form fields that will be populated by PDF parsing -->
                            <input type="hidden" id="nama_pembekal" name="nama_pembekal">
                            <input type="hidden" id="nombor_telefon" name="nombor_telefon">
                            <input type="hidden" id="nombor_pesanan" name="nombor_pesanan">
                            <input type="hidden" id="jumlah_harga" name="jumlah_harga">
                            <input type="hidden" id="tarikh_pesanan" name="tarikh_pesanan">
                            <input type="hidden" id="tarikh_tamat" name="tarikh_tamat">
                            
                            <!-- Main Upload Area -->
                            <div class="main-upload-area" id="mainUploadArea">
                                <div class="text-center mb-4">
                                    <h6 class="text-muted">Muat Naik Dokumen PDF Pesanan Belian</h6>
                                    <p class="small text-muted">Sistem akan mengekstrak maklumat secara automatik dari PDF</p>
                                </div>

                                <!-- PDF File Upload Section -->
                                <div class="mb-4">
                                    <div class="file-drop-zone" id="fileDropZone">
                                        <i class="ti ti-cloud-upload fs-1 text-primary mb-3"></i>
                                        <h5 class="mb-2">Seret dan lepas fail PDF di sini</h5>
                                        <p class="mb-3">atau klik untuk pilih fail</p>
                                        <button type="button" class="btn btn-primary">
                                            <i class="ti ti-file-upload me-2"></i>Pilih Fail PDF
                                        </button>
                                        <small class="d-block mt-3 text-muted">Format yang diterima: PDF (Maksimum 10MB)</small>
                                    </div>
                                    
                                    <div class="file-info" id="fileInfo">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-file-text me-2 text-primary"></i>
                                            <span id="fileName"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="removeFile">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-info btn-parse" id="parseBtn">
                                        <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status"></span>
                                        <i class="ti ti-scan me-1"></i>
                                        Analisis PDF
                                    </button>
                                    
                                    <!-- Debug/Test Button (remove in production) -->
                                    <button type="button" class="btn btn-sm btn-warning mt-2" id="testUrlBtn">
                                        <i class="ti ti-bug me-1"></i>Test URL
                                    </button>
                                    
                                    <!-- Debug PDF Text Button (remove in production) -->
                                    <button type="button" class="btn btn-sm btn-info mt-2 ms-2" id="debugPdfBtn">
                                        <i class="ti ti-file-text me-1"></i>Debug PDF Text
                                    </button>

                                    <!-- Parsing Progress -->
                                    <div class="parsing-progress" id="parsingProgress">
                                        <div class="d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm text-info me-2" role="status"></div>
                                            <span>Sedang menganalisis PDF...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PDF Analysis Results -->
                            <div class="pdf-preview" id="pdfPreview">
                                <h6 class="fw-semibold mb-3">
                                    <i class="ti ti-file-analytics me-1"></i>
                                    Maklumat Yang Diekstrak dari PDF
                                </h6>
                                <div id="pdfContent"></div>
                                
                                <!-- Data Summary -->
                                <div class="data-summary" id="dataSummary">
                                    <h6 class="fw-semibold text-success mb-2">
                                        <i class="ti ti-check-circle me-1"></i>
                                        Ringkasan Data Peringatan
                                    </h6>
                                    <div id="summaryContent"></div>
                                </div>

                                <!-- Save Actions -->
                                <div class="save-actions" id="saveActions">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="confirmData" checked>
                                            <label class="form-check-label" for="confirmData">
                                                Saya mengesahkan maklumat ini adalah betul
                                            </label>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-secondary" id="analyzeAgain">
                                                <i class="ti ti-refresh me-1"></i>
                                                Analisis Semula
                                            </button>
                                            <button type="button" class="btn btn-success" id="saveFromPDF">
                                                <i class="ti ti-device-floppy me-1"></i>
                                                Simpan Peringatan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4" id="bottomActions">
                                <a href="<?php echo current_url(); ?>/../listreminders" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>
                                    Kembali
                                </a>
                                <button type="button" class="btn btn-outline-warning" id="resetBtn">
                                    <i class="ti ti-refresh me-1"></i>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Configuration - try multiple URL patterns for your setup
            const currentUrl = window.location.href;
            const currentPath = window.location.pathname; // /myprojek/manage/reminder/addForm
            
            // Extract different possible base paths
            const pathParts = currentPath.split('/');
            
            // Multiple URL strategies for your setup
            const urlCandidates = [
                // Strategy 1: Replace addForm with parsePdfAjax
                currentUrl.replace('/addForm', '/parsePdfAjax'),
                
                // Strategy 2: Direct controller method (if routes exist)
                window.location.origin + currentPath.replace('/addForm', '/parsePdfAjax'),
                
                // Strategy 3: Without /manage/ prefix
                window.location.origin + '/' + pathParts[1] + '/reminder/parsePdfAjax',
                
                // Strategy 4: With index.php
                window.location.origin + '/' + pathParts[1] + '/index.php/reminder/parsePdfAjax',
                
                // Strategy 5: Direct to controller (no manage prefix)
                window.location.origin + '/' + pathParts[1] + '/index.php/manage/reminder/parsePdfAjax'
            ];
            
            // Use the first candidate as primary
            let AJAX_URL = urlCandidates[0];
            
            const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
            
            console.log('PDF Upload System initialized');
            console.log('Current URL:', currentUrl);
            console.log('Current Path:', currentPath);
            console.log('URL Candidates:', urlCandidates);
            console.log('Primary AJAX URL:', AJAX_URL);
            
            // Make urlCandidates accessible to other functions
            window.urlCandidates = urlCandidates;
            
            // DOM Elements
            const elements = {
                fileInput: document.getElementById('pdf_file'),
                fileDropZone: document.getElementById('fileDropZone'),
                fileInfo: document.getElementById('fileInfo'),
                fileName: document.getElementById('fileName'),
                removeFileBtn: document.getElementById('removeFile'),
                parseBtn: document.getElementById('parseBtn'),
                pdfPreview: document.getElementById('pdfPreview'),
                pdfContent: document.getElementById('pdfContent'),
                dataSummary: document.getElementById('dataSummary'),
                summaryContent: document.getElementById('summaryContent'),
                saveActions: document.getElementById('saveActions'),
                loadingSpinner: document.querySelector('.loading-spinner'),
                parsingProgress: document.getElementById('parsingProgress'),
                saveFromPDFBtn: document.getElementById('saveFromPDF'),
                analyzeAgainBtn: document.getElementById('analyzeAgain'),
                confirmDataCheck: document.getElementById('confirmData'),
                reminderForm: document.getElementById('reminderForm'),
                errorAlert: document.getElementById('errorAlert'),
                errorList: document.getElementById('errorList'),
                successAlert: document.getElementById('successAlert'),
                successMessage: document.getElementById('successMessage'),
                mainUploadArea: document.getElementById('mainUploadArea'),
                resetBtn: document.getElementById('resetBtn'),
                testUrlBtn: document.getElementById('testUrlBtn'),
                debugPdfBtn: document.getElementById('debugPdfBtn')
            };
            
            let extractedData = {};

            // Utility Functions
            function showError(errors) {
                elements.errorList.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    elements.errorList.appendChild(li);
                });
                elements.errorAlert.classList.remove('d-none');
                elements.successAlert.classList.add('d-none');
            }

            function hideError() {
                elements.errorAlert.classList.add('d-none');
            }

            function showSuccess(message) {
                elements.successMessage.textContent = message;
                elements.successAlert.classList.remove('d-none');
                elements.errorAlert.classList.add('d-none');
            }

            function hideSuccess() {
                elements.successAlert.classList.add('d-none');
            }

            function scrollToElement(element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // File Handling
            function setupFileDropZone() {
                elements.fileDropZone.addEventListener('click', function(e) {
                    e.preventDefault();
                    elements.fileInput.click();
                });

                elements.fileDropZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    elements.fileDropZone.classList.add('dragover');
                });
                
                elements.fileDropZone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    elements.fileDropZone.classList.remove('dragover');
                });
                
                elements.fileDropZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    elements.fileDropZone.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleDroppedFiles(files);
                    }
                });
            }

            function handleDroppedFiles(files) {
                const file = files[0];
                if (file && file.type === 'application/pdf') {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    elements.fileInput.files = dt.files;
                    handleFileSelect();
                } else {
                    showError(['Sila pilih fail PDF sahaja']);
                }
            }

            function setupFileInput() {
                elements.fileInput.addEventListener('change', function(e) {
                    handleFileSelect();
                });
            }

            function handleFileSelect() {
                const file = elements.fileInput.files[0];
                
                if (file) {
                    console.log('File selected:', file.name, 'Size:', file.size);
                    
                    if (file.type !== 'application/pdf') {
                        showError(['Sila pilih fail PDF sahaja']);
                        return;
                    }
                    if (file.size > MAX_FILE_SIZE) {
                        showError(['Saiz fail melebihi had 10MB']);
                        return;
                    }
                    
                    elements.fileName.textContent = file.name;
                    elements.fileInfo.style.display = 'block';
                    elements.fileDropZone.style.display = 'none';
                    hideError();
                    
                    console.log('File ready for analysis. Click "Analisis PDF" or "Debug PDF Text"');
                    showSuccess('Fail PDF siap untuk dianalisis. Klik "Analisis PDF" untuk mula atau "Debug PDF Text" untuk melihat kandungan mentah.');
                    
                    // DON'T auto-analyze anymore - let user choose
                    // setTimeout(parsePDF, 500); // REMOVED
                }
            }

            function removeFile() {
                elements.fileInput.value = '';
                elements.fileInfo.style.display = 'none';
                elements.pdfPreview.style.display = 'none';
                elements.saveActions.style.display = 'none';
                elements.parsingProgress.style.display = 'none';
                elements.fileDropZone.style.display = 'block';
                elements.mainUploadArea.style.display = 'block';
                extractedData = {};
                hideError();
                hideSuccess();
                console.log('File removed');
            }

            function parsePDF() {
                const file = elements.fileInput.files[0];
                if (!file) {
                    showError(['Tiada fail PDF dipilih']);
                    return;
                }

                console.log('Starting PDF parsing:', file.name);
                console.log('Using AJAX URL:', AJAX_URL);

                // Show loading state
                elements.loadingSpinner.style.display = 'inline-block';
                elements.parsingProgress.style.display = 'block';
                elements.parseBtn.disabled = true;
                if (elements.analyzeAgainBtn) elements.analyzeAgainBtn.disabled = true;
                elements.pdfPreview.style.display = 'none';
                elements.mainUploadArea.style.display = 'none';
                hideError();
                hideSuccess();

                // Create FormData
                const formData = new FormData();
                formData.append('pdf_file', file);
                
                const csrfToken = document.getElementById('csrfToken').value;
                formData.append('csrf_token', csrfToken);

                // Test URL accessibility first
                console.log('Testing URL accessibility...');
                
                // Make AJAX request with better error handling
                fetch(AJAX_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response received:', response.status, response.statusText);
                    console.log('Response URL:', response.url);
                    
                    if (response.status === 404) {
                        throw new Error(`URL tidak ditemui: ${AJAX_URL}\nSila pastikan route dan controller telah dikonfigurasi dengan betul.`);
                    }
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
                    }
                    
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error(`Server tidak mengembalikan JSON. Content-Type: ${contentType}`);
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Parsed response data:', data);
                    
                    // Hide loading state
                    elements.loadingSpinner.style.display = 'none';
                    elements.parsingProgress.style.display = 'none';
                    elements.parseBtn.disabled = false;
                    if (elements.analyzeAgainBtn) elements.analyzeAgainBtn.disabled = false;

                    if (data.status === 'success') {
                        extractedData = data;
                        displayExtractedData();
                        populateFormFields();
                        showSuccess('PDF berjaya dianalisis!');
                    } else {
                        showError([data.message || 'Ralat semasa menganalisis PDF']);
                        elements.mainUploadArea.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error during PDF parsing:', error);
                    
                    // Hide loading state
                    elements.loadingSpinner.style.display = 'none';
                    elements.parsingProgress.style.display = 'none';
                    elements.parseBtn.disabled = false;
                    if (elements.analyzeAgainBtn) elements.analyzeAgainBtn.disabled = false;

                    let errorMessage = 'Ralat semasa menganalisis PDF: ' + error.message;
                    
                    // Add debugging information for 404 errors
                    if (error.message.includes('404') || error.message.includes('tidak ditemui')) {
                        errorMessage += '\n\nDebugging Info:';
                        errorMessage += '\n- URL yang dicuba: ' + AJAX_URL;
                        errorMessage += '\n- Pastikan route "reminder/parsePdfAjax" telah dikonfigurasi';
                        errorMessage += '\n- Pastikan method parsePdfAjax() wujud dalam Reminder controller';
                    }
                    
                    showError([errorMessage]);
                    elements.mainUploadArea.style.display = 'block';
                });
            }

            function populateFormFields() {
                const fieldMapping = {
                    'nama_pembekal': extractedData.supplier_name,
                    'nombor_telefon': extractedData.phone_number,
                    'nombor_pesanan': extractedData.order_number,
                    'jumlah_harga': extractedData.total_amount ? extractedData.total_amount.toString().replace(/[RM\s,]/g, '') : '',
                    'tarikh_pesanan': extractedData.order_date ? convertDateFormat(extractedData.order_date) : '',
                    'tarikh_tamat': extractedData.due_date ? convertDateFormat(extractedData.due_date) : ''
                };

                Object.keys(fieldMapping).forEach(fieldId => {
                    const element = document.getElementById(fieldId);
                    if (element && fieldMapping[fieldId]) {
                        element.value = fieldMapping[fieldId];
                    }
                });
            }

            function convertDateFormat(dateString) {
                if (!dateString) return '';
                
                try {
                    // Handle DD-Mon-YYYY format
                    if (dateString.includes('-') && dateString.split('-').length === 3) {
                        const parts = dateString.split('-');
                        if (parts[1].length === 3) {
                            const monthMap = {
                                'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                                'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                                'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                            };
                            const month = monthMap[parts[1]];
                            if (month) {
                                return `${parts[2]}-${month}-${parts[0].padStart(2, '0')}`;
                            }
                        }
                    }
                    
                    const date = new Date(dateString);
                    if (!isNaN(date.getTime())) {
                        return date.toISOString().split('T')[0];
                    }
                    
                    return '';
                } catch (e) {
                    console.error('Date conversion error:', e);
                    return '';
                }
            }

            function saveFromPDF() {
                if (!extractedData.supplier_name && !extractedData.order_number && !extractedData.phone_number) {
                    showError(['Data tidak lengkap untuk menyimpan peringatan']);
                    return;
                }

                if (!elements.confirmDataCheck.checked) {
                    showError(['Sila sahkan maklumat sebelum menyimpan']);
                    return;
                }

                populateFormFields();
                
                elements.saveFromPDFBtn.disabled = true;
                elements.saveFromPDFBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                
                elements.reminderForm.submit();
            }

            function displayExtractedData() {
                if (!extractedData || extractedData.status !== 'success') {
                    showError(['Tiada data yang berjaya diekstrak dari PDF']);
                    return;
                }

                let html = '';
                let summaryHtml = '';
                let hasRequiredData = false;
                
                const dataFields = [
                    { key: 'supplier_name', label: 'Nama Pembekal', summary: 'Pembekal', required: true },
                    { key: 'order_number', label: 'Nombor Pesanan', summary: 'No. Pesanan', required: true },
                    { key: 'phone_number', label: 'Nombor Telefon', summary: 'Telefon', required: true },
                    { key: 'order_date', label: 'Tarikh Pesanan', summary: 'Tarikh Mula', required: false },
                    { key: 'due_date', label: 'Tarikh Tamat', summary: 'Tarikh Tamat', required: false },
                    { key: 'total_amount', label: 'Jumlah Harga', summary: 'Jumlah', required: false }
                ];

                dataFields.forEach(field => {
                    if (extractedData[field.key]) {
                        html += `<div class="pdf-info-card">
                            <strong>${field.label}:</strong> ${extractedData[field.key]}
                        </div>`;
                        summaryHtml += `<div><strong>${field.summary}:</strong> ${extractedData[field.key]}</div>`;
                        if (field.required) hasRequiredData = true;
                    }
                });

                if (!hasRequiredData) {
                    html += `<div class="pdf-info-card warning">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle text-warning me-2"></i>
                            <span>Maklumat penting tidak ditemui. Sila semak fail PDF anda.</span>
                        </div>
                    </div>`;
                }

                elements.pdfContent.innerHTML = html;
                elements.summaryContent.innerHTML = summaryHtml;
                elements.pdfPreview.style.display = 'block';
                elements.pdfPreview.classList.add('success-animation');
                
                if (hasRequiredData) {
                    elements.dataSummary.style.display = 'block';
                    elements.saveActions.style.display = 'block';
                }

                scrollToElement(elements.pdfPreview);
            }

            function resetForm() {
                elements.reminderForm.reset();
                removeFile();
                extractedData = {};
                hideError();
                hideSuccess();
            }

            // Test URL function - now tests multiple candidates
            function testUrl() {
                const urlsToTest = window.urlCandidates || urlCandidates;
                console.log('Testing multiple URL candidates...');
                showSuccess('Testing multiple URL patterns...');
                
                let testResults = [];
                let testIndex = 0;
                
                function testNextUrl() {
                    if (testIndex >= urlsToTest.length) {
                        // All tests completed
                        displayTestResults(testResults);
                        return;
                    }
                    
                    const testUrl = urlsToTest[testIndex];
                    console.log(`Testing URL ${testIndex + 1}:`, testUrl);
                    
                    fetch(testUrl, { 
                        method: 'GET',
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        testResults.push({
                            url: testUrl,
                            status: response.status,
                            statusText: response.statusText,
                            success: response.status === 200
                        });
                        testIndex++;
                        testNextUrl();
                    })
                    .catch(error => {
                        testResults.push({
                            url: testUrl,
                            status: 'ERROR',
                            statusText: error.message,
                            success: false
                        });
                        testIndex++;
                        testNextUrl();
                    });
                }
                
                function displayTestResults(results) {
                    console.log('Test results:', results);
                    
                    const workingUrl = results.find(r => r.success);
                    if (workingUrl) {
                        AJAX_URL = workingUrl.url;
                        showSuccess('Found working URL: ' + AJAX_URL);
                    } else {
                        let errorMessages = ['No working URL found. Test results:'];
                        results.forEach((result, index) => {
                            errorMessages.push(`${index + 1}. ${result.url} → ${result.status} ${result.statusText}`);
                        });
                        errorMessages.push('');
                        errorMessages.push('Suggestions:');
                        errorMessages.push('1. Check your routes.php file');
                        errorMessages.push('2. Make sure parsePdfAjax method exists');
                        errorMessages.push('3. Check your controller is accessible');
                        showError(errorMessages);
                    }
                }
                
                testNextUrl();
            }

            // Debug PDF text function - with better error handling
            function debugPdfText() {
                const file = elements.fileInput.files[0];
                if (!file) {
                    showError(['Sila pilih fail PDF dahulu']);
                    return;
                }

                console.log('Debugging PDF text for:', file.name);
                showSuccess('Mengekstrak teks mentah dari PDF...');

                const formData = new FormData();
                formData.append('pdf_file', file);
                formData.append('debug_mode', 'true'); // Enable debug mode
                
                const csrfToken = document.getElementById('csrfToken').value;
                formData.append('csrf_token', csrfToken);

                // Use the existing AJAX URL but with debug mode
                fetch(AJAX_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        // If not JSON, get as text to see the actual error
                        return response.text().then(text => {
                            console.log('Non-JSON response received:', text);
                            throw new Error('Server returned HTML instead of JSON. Possible PHP error.');
                        });
                    }
                })
                .then(data => {
                    console.log('Debug response received:', data);
                    
                    if (data.debug_raw_text) {
                        // Open debug text in new window
                        const newWindow = window.open('', '_blank', 'width=1000,height=700,scrollbars=yes');
                        newWindow.document.write(`
                            <html>
                            <head>
                                <title>PDF Debug Text - ${file.name}</title>
                                <style>
                                    body { font-family: monospace; margin: 20px; }
                                    .info { background: #e3f2fd; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
                                    .raw-text { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; }
                                    .extraction-test { background: #e8f5e8; padding: 10px; margin-top: 20px; border-radius: 5px; }
                                </style>
                            </head>
                            <body>
                                <h2>PDF Debug Information</h2>
                                <div class="info">
                                    <strong>File:</strong> ${file.name}<br>
                                    <strong>Size:</strong> ${file.size} bytes<br>
                                    <strong>Text Length:</strong> ${data.debug_text_length || 'Unknown'} characters<br>
                                    <strong>Parsing Method:</strong> ${data.parsing_method || 'Unknown'}
                                </div>
                                
                                <h3>Extraction Test Results:</h3>
                                <div class="extraction-test">
                                    <strong>Supplier Name:</strong> ${data.supplier_name || 'NOT FOUND'}<br>
                                    <strong>Order Number:</strong> ${data.order_number || 'NOT FOUND'}<br>
                                    <strong>Phone Number:</strong> ${data.phone_number || 'NOT FOUND'}<br>
                                    <strong>Order Date:</strong> ${data.order_date || 'NOT FOUND'}<br>
                                    <strong>Due Date:</strong> ${data.due_date || 'NOT FOUND'}<br>
                                    <strong>Total Amount:</strong> ${data.total_amount || 'NOT FOUND'}
                                </div>
                                
                                <h3>Raw PDF Text:</h3>
                                <div class="raw-text">${data.debug_raw_text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</div>
                            </body>
                            </html>
                        `);
                        
                        showSuccess('Debug window opened! Check the extraction results and raw text.');
                    } else if (data.debug_error) {
                        showError(['Debug error: ' + data.debug_error]);
                    } else if (data.status === 'error') {
                        showError(['PDF parsing error: ' + (data.message || 'Unknown error')]);
                    } else {
                        showError(['No debug information available in response']);
                    }
                })
                .catch(error => {
                    console.error('Debug error:', error);
                    let errorMsg = 'Error debugging PDF: ' + error.message;
                    
                    if (error.message.includes('JSON')) {
                        errorMsg += '\n\nThis usually means there\'s a PHP syntax error in the controller or library.';
                        errorMsg += '\nCheck your server error logs or try a simpler test first.';
                    }
                    
                    showError([errorMsg]);
                });
            }

            // Event Listeners
            setupFileDropZone();
            setupFileInput();
            
            elements.removeFileBtn.addEventListener('click', removeFile);
            elements.parseBtn.addEventListener('click', parsePDF);
            elements.saveFromPDFBtn.addEventListener('click', saveFromPDF);
            if (elements.analyzeAgainBtn) {
                elements.analyzeAgainBtn.addEventListener('click', parsePDF);
            }
            elements.confirmDataCheck.addEventListener('change', function() {
                elements.saveFromPDFBtn.disabled = !this.checked;
            });
            elements.resetBtn.addEventListener('click', resetForm);
            elements.testUrlBtn.addEventListener('click', testUrl);
            elements.debugPdfBtn.addEventListener('click', debugPdfText);
            
            console.log('PDF Upload System ready');
        });
    </script>
</body>
</html>