<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $this->security->get_csrf_hash(); ?>">
    <title>Patient Statistics Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-card {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-header-simple {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.25rem;
            border-radius: 8px 8px 0 0;
        }

        .filters-container {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .form-select, .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        .form-select:focus, .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-generate {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            border-radius: 4px;
            padding: 0.375rem 0.75rem;
        }

        .btn-generate:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            color: white;
        }

        .chart-container {
            position: relative;
            height: 450px; /* Increased height to accommodate labels */
            background: white;
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 6px;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .error {
            color: #dc3545;
            text-align: center;
            padding: 1rem;
            background: #f8d7da;
            border-radius: 4px;
            margin: 1rem 0;
            border: 1px solid #f5c6cb;
        }

        .debug-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
            font-family: monospace;
            font-size: 0.875rem;
            white-space: pre-wrap;
            border: 1px solid #bee5eb;
        }

        .back-btn {
            background-color: #198754;
            border-color: #198754;
            color: white;
            border-radius: 4px;
            padding: 0.375rem 0.75rem;
        }

        .back-btn:hover {
            background-color: #157347;
            border-color: #146c43;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .page-title {
            color: #495057;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Color coding for Bahagian Utama rows */
        .bahagian-header.skull-head {
            background-color: #e3f2fd; /* Light blue */
        }
        .bahagian-header.spine {
            background-color: #e8f5e9; /* Light green */
        }
        .bahagian-header.chest {
            background-color: #fff3e0; /* Light orange */
        }
        .bahagian-header.abdomen {
            background-color: #fce4ec; /* Light pink */
        }
        .bahagian-header.upper-extremities {
            background-color: #f3e5f5; /* Light purple */
        }
        .bahagian-header.lower-extremities {
            background-color: #e0f7fa; /* Light cyan */
        }

        /* Hover effects */
        .bahagian-header:hover {
            opacity: 0.9;
        }

        /* Grand total row styling */
        .grand-total-row {
            background-color: #e8eaf6 !important; /* Different color for grand total */
            font-weight: bold;
        }

        /* Sub-bahagian row styling */
        .sub-bahagian-row {
            background-color: #f5f5f5;
        }

        .sub-bahagian-row:hover {
            background-color: #eeeeee;
        }

        /* Bahagian Utama cell styling */
        .bahagian-utama-cell {
            font-weight: bold;
            border-left: 4px solid transparent;
        }

        /* Specific border colors for each Bahagian Utama */
        .bahagian-header.skull-head .bahagian-utama-cell {
            border-left-color: #1976d2; /* Blue */
        }
        .bahagian-header.spine .bahagian-utama-cell {
            border-left-color: #388e3c; /* Green */
        }
        .bahagian-header.chest .bahagian-utama-cell {
            border-left-color: #ffa000; /* Amber */
        }
        .bahagian-header.abdomen .bahagian-utama-cell {
            border-left-color: #d81b60; /* Pink */
        }
        .bahagian-header.upper-extremities .bahagian-utama-cell {
            border-left-color: #8e24aa; /* Purple */
        }
        .bahagian-header.lower-extremities .bahagian-utama-cell {
            border-left-color: #00acc1; /* Cyan */
        }

        /* Chart type indicator */
        .chart-type-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 10;
        }

        /* Remark Modal Styles */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1060;
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        /* Table header with remark button */
        .table-header-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        /* Bootstrap CSS for layout */
        .container-fluid { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
        .col-lg-6 { flex: 0 0 50%; max-width: 50%; padding: 0 15px; }
        .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 0 15px; }
        .col-md-3 { flex: 0 0 25%; max-width: 25%; padding: 0 15px; }
        .col-md-2 { flex: 0 0 16.666667%; max-width: 16.666667%; padding: 0 15px; }
        .mb-4 { margin-bottom: 1.5rem; }
        .py-4 { padding: 1.5rem 0; }
        .h-100 { height: 100%; }
        .card-body { padding: 1rem; }
        .g-3 > * { margin-bottom: 1rem; }
        .d-flex { display: flex; }
        .align-items-end { align-items: flex-end; }
        .w-100 { width: 100%; }
        .text-muted { color: #6c757d; }
        .me-2 { margin-right: 0.5rem; }
        .form-label { margin-bottom: 0.5rem; font-weight: 500; }
        .btn { padding: 0.375rem 0.75rem; border: 1px solid transparent; border-radius: 0.25rem; cursor: pointer; }
        .visually-hidden { display: none; }
        .spinner-border { width: 2rem; height: 2rem; border: 0.25em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border 0.75s linear infinite; }
        @keyframes spinner-border { to { transform: rotate(360deg); } }
        .mt-2 { margin-top: 0.5rem; }
        .table-responsive { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; border-bottom: 1px solid #dee2e6; }
        .table-light { background-color: #f8f9fa; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }

        @media (max-width: 992px) {
            .col-lg-6 { flex: 0 0 100%; max-width: 100%; }
        }
        @media (max-width: 768px) {
            .col-md-4, .col-md-3, .col-md-2 { flex: 0 0 100%; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Simple Page Title -->
        <div class="page-title">
            <h2>Patient Statistics Dashboard</h2>
            <p class="text-muted">View patient distribution and reports</p>
        </div>

        <!-- Back Button -->
        <div class="mb-4">
            <button class="btn back-btn" onclick="goBack()">
                <i class="bi bi-arrow-left me-2"></i> Back
            </button>
        </div>

        <!-- Unified Filter Section -->
<div class="unified-filters">
    <h5><i class="fas fa-filter me-2"></i>Filter Controls</h5>
    <div class="filters-container">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="bahagian_utama" class="form-label">Bahagian Utama *</label>
                <select id="bahagian_utama" class="form-select" required>
                    <option value="">All Bahagian Utama</option>
                    <option value="Skull and Head">Skull and Head</option>
                    <option value="Spine">Spine</option>
                    <option value="Chest">Chest</option>
                    <option value="Abdomen">Abdomen</option>
                    <option value="Upper Extremities">Upper Extremities</option>
                    <option value="Lower Extremities">Lower Extremities</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="month" class="form-label">Month</label>
                <select id="month" class="form-select">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="year" class="form-label">Year</label>
                <select id="year" class="form-select">
                    <option value="">All Years</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-generate w-100" onclick="generateBoth()">
                    Generate All
                </button>
            </div>
        </div>
    </div>
</div>

        <div class="row">
            <!-- Graph Card -->
            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header-simple">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Patient Statistics Graph</h5>
                    </div>
                    <div class="card-body">
                        <!-- Loading -->
                        <div id="loading" class="loading" style="display: none;">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">Loading chart data...</p>
                        </div>

                        <!-- Error Display -->
                        <div id="error" class="error" style="display: none;"></div>
                        
                        <!-- Debug Info -->
                        <div id="debug-info" class="debug-info" style="display: none;"></div>

                        <!-- Chart Container -->
                        <div class="chart-container">
                            <div id="chart-type-indicator" class="chart-type-indicator" style="display: none;"></div>
                            <canvas id="patientChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Reporting Card -->
            <div class="col-lg-6 mb-4">
                <div class="dashboard-card h-100">
                    <div class="card-header-simple">
                        <div class="table-header-container">
                            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Table Reporting</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Table Loading -->
                        <div id="table-loading" class="loading" style="display: none;">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">Loading table data...</p>
                        </div>

                        <!-- Table Error Display -->
                        <div id="table-error" class="error" style="display: none;"></div>

                        <!-- Table Content -->
                        <div id="table-content">
                            <div class="empty-state">
                                <i class="fas fa-table"></i>
                                <h5>Table Reporting</h5>
                                <p class="text-muted">Use the filters above and click Generate All to view table data.</p>
                            </div>
                        </div>
                        <!-- Remark Button (moved under table) -->
                        <div class="mt-3 text-end">
                            <button class="btn btn-outline-primary" onclick="openRemarkModal()" title="Add Remark">
                                <i class="fas fa-comment-alt me-2"></i>Remark
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remark Modal -->
    <div id="remarkModal" style="display: none;">
        <div class="modal-backdrop" onclick="closeRemarkModal()"></div>
        <div class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">Add Remark</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <small class="text-muted" id="remarkContext">
                            <!-- This will be populated by JavaScript -->
                        </small>
                    </div>
                    <label for="remarkText" class="form-label">Enter your remark:</label>
                    <textarea id="remarkText" class="form-control" rows="4" placeholder="Add your notes or comments here..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeRemarkModal()">Cancel</button>
                    <button type="button" class="btn btn-generate" onclick="saveRemark()">Save Remark</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        // NEW: Function to generate both graph and table with unified filters
function generateBoth() {
    generateGraph();
    generateTable();
}
    function openRemarkModal() {
    // Get current filter values
    const bahagianUtama = document.getElementById('bahagian_utama').value;
    const bulan = document.getElementById('month').value;
    const tahun = document.getElementById('year').value;
    
    // Validate filters
    if (!bahagianUtama || !bulan || !tahun) {
        alert('Please select Bahagian Utama, Month, and Year first');
        return;
    }
    
    // Set context info
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('remarkContext').textContent = 
        `Remark for: ${bahagianUtama} - ${monthNames[bulan-1]} ${tahun}`;
    
    // Store current filter values
    document.getElementById('remarkModal').setAttribute('data-bahagian-utama', bahagianUtama);
    document.getElementById('remarkModal').setAttribute('data-bulan', bulan);
    document.getElementById('remarkModal').setAttribute('data-tahun', tahun);
    
    // Load existing remark
    loadExistingRemark(bahagianUtama, bulan, tahun);
    
    document.getElementById("remarkModal").style.display = "block";
}

function loadExistingRemark(bahagianUtama, bulan, tahun) {
    const formData = new FormData();
    formData.append('bahagian_utama', bahagianUtama);
    formData.append('bulan', bulan);
    formData.append('tahun', tahun);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', getCSRFToken());
    
    fetch('get_remark', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        console.log('Get remark response:', text);
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                document.getElementById('remarkText').value = data.remark || '';
            }
        } catch (e) {
            console.error('Error parsing get_remark response:', e);
        }
    })
    .catch(error => {
        console.error('Error loading remark:', error);
    });
}

function closeRemarkModal() {
    document.getElementById("remarkModal").style.display = "none";
}

// Replace the saveRemark function in your patient_graph HTML file
function saveRemark() {
    const remark = document.getElementById('remarkText').value;
    const bahagianUtama = document.getElementById('remarkModal').getAttribute('data-bahagian-utama');
    const bulan = document.getElementById('remarkModal').getAttribute('data-bulan');
    const tahun = document.getElementById('remarkModal').getAttribute('data-tahun');
    
    const formData = new FormData();
    formData.append('bahagian_utama', bahagianUtama);
    formData.append('bulan', bulan);
    formData.append('tahun', tahun);
    formData.append('remark', remark);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', getCSRFToken());
    
    fetch('save_remark', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                let message = data.message;
                if (!data.patient_data_exists) {
                    message += '\nThis remark is for reference only - no patient data matches these exact filters.';
                }

                closeRemarkModal();

                // Ask user whether to export PDF
                if (confirm(message + "\n\nDo you want to download this report as PDF?")) {
                    // FIXED: Use POST method instead of GET
                    exportPDFWithPOST(bahagianUtama, bulan, tahun);
                } else {
                    alert("Remark saved successfully without exporting PDF.");
                }

            } else {
                alert('Error saving remark: ' + data.message);
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', text);
            alert('Server returned invalid JSON. Check console for details.');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Error saving remark: ' + error.message);
    });
}

// NEW: Function to export PDF using POST method
function exportPDFWithPOST(bahagianUtama, bulan, tahun) {
    // Create a temporary form to submit POST data
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'export_pdf';
    form.target = '_blank'; // Open PDF in new window
    form.style.display = 'none'; // Hide the form

    // Add form fields
    const fields = [
        { name: 'bahagian_utama', value: bahagianUtama },
        { name: 'bulan', value: bulan },
        { name: 'tahun', value: tahun },
        { name: '<?php echo $this->security->get_csrf_token_name(); ?>', value: getCSRFToken() }
    ];

    fields.forEach(field => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = field.name;
        input.value = field.value;
        form.appendChild(input);
    });

    // Add form to document, submit it, then remove it
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// OPTIONAL: Add a standalone PDF export button if needed
function exportPDFStandalone() {
    const bahagianUtama = document.getElementById('table_bahagian_utama').value;
    const bulan = document.getElementById('table_month').value;
    const tahun = document.getElementById('table_year').value;
    
    if (!bahagianUtama || !bulan || !tahun) {
        alert('Please select Bahagian Utama, Month, and Year first');
        return;
    }
    
    exportPDFWithPOST(bahagianUtama, bulan, tahun);
}

// Function to get CSRF token from meta tag
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}
        // Add this to your existing JavaScript
        function loadAvailableYears() {
            fetch('get_available_years')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update year dropdown
updateYearDropdown('year', data.years);
                    }
                })
                .catch(error => {
                    console.error('Error loading available years:', error);
                });
        }

        function updateYearDropdown(elementId, years) {
            const select = document.getElementById(elementId);
            
            // Keep the first option (All Years)
            while (select.options.length > 1) {
                select.remove(1);
            }
            
            // Add years in descending order
            years.sort((a, b) => b - a).forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                select.appendChild(option);
            });
        }

        // Call this on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAvailableYears();
            
            // Also keep your existing current year logic as a fallback
            const currentYear = new Date().getFullYear();
            const yearSelects = ['year'];
            
            yearSelects.forEach(selectId => {
                const yearSelect = document.getElementById(selectId);
                const existingOption = Array.from(yearSelect.options).find(option => option.value == currentYear);
                if (!existingOption && currentYear > 2024) {
                    const option = new Option(currentYear, currentYear);
                    yearSelect.insertBefore(option, yearSelect.options[1]);
                }
            });

            // Demo the updated chart with sample data
            
        });

        let chart = null;
        let currentChartType = 'grouped'; // Track current chart type
        
        // Define the base URL - adjust this to match your CodeIgniter setup
        const BASE_URL = window.location.origin + window.location.pathname.replace('/patient_graph', '');

        function generateGraph() {
            const bahagianUtama = document.getElementById('bahagian_utama').value;
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;

            // Show loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            document.getElementById('debug-info').style.display = 'none';

            // Prepare form data
            const formData = new FormData();
            formData.append('bahagian_utama', bahagianUtama); // Empty string for "All"
            if (month) formData.append('month', month);
            if (year) formData.append('year', year);

            // Use relative URL that should work with most CodeIgniter setups
            const url = 'get_graph_data';
            
            console.log('Making request to:', url);

            // Make AJAX request
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Check if response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Get response text first to see what we're getting
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                
                try {
                    // Try to parse as JSON
                    const data = JSON.parse(text);
                    document.getElementById('loading').style.display = 'none';
                    
                    if (data.status === 'success') {
                        // Set current chart type
                        currentChartType = data.chart_type || 'grouped';
                        renderChart(data.data, currentChartType);
                        updateChartTypeIndicator(currentChartType, bahagianUtama);
                    } else {
                        showError(data.message || 'Failed to load graph data');
                    }
                } catch (e) {
                    // If JSON parsing fails, show the raw response
                    document.getElementById('loading').style.display = 'none';
                    showError('Invalid JSON response. Raw response: ' + text.substring(0, 500));
                }
            })
            .catch(error => {
                document.getElementById('loading').style.display = 'none';
                console.error('Fetch error:', error);
                showError('Error loading graph data: ' + error.message);
            });
        }

        function updateChartTypeIndicator(chartType, bahagianUtama) {
            const indicator = document.getElementById('chart-type-indicator');
            if (chartType === 'stacked') {
                //indicator.textContent = 'Stacked by Bahagian Utama';
                indicator.style.display = 'block';
            } else {
              //indicator.textContent = `Grouped by Gender - ${bahagianUtama}`;
                indicator.style.display = 'block';
            }
        }

        // UPDATED: Enhanced renderChart function with month separators
        function renderChart(data, chartType = 'grouped') {
            const ctx = document.getElementById('patientChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (chart) {
                chart.destroy();
            }

            // Chart configuration based on type
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            bottom: 70 // Increased padding for proper label spacing
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Patients'
                            },
                            ticks: {
                                stepSize: 1
                            },
                            stacked: chartType === 'stacked'
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month and Gender',
                                 padding: {
            top: 60  // Pushes the title lower, closer under the month labels
        },
        align: 'center' // Ensures it spans across the full x-axis                 
                            },
                            ticks: {
                                display: false // Hide default x-axis labels - we'll draw custom ones
                            },
                            grid: {
                                display: true
                            },
                            stacked: chartType === 'stacked'
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: chartType === 'stacked' ? 
                                'Patient Distribution by Bahagian Utama (Stacked by Month-Gender)' : 
                                'Patient Distribution by Gender and Month',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                generateLabels: function(chart) {
                                    const original = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                    return original.map(label => {
                                        if (label.text === 'Lelaki') label.text = 'L';
                                        if (label.text === 'Perempuan') label.text = 'P';
                                        return label;
                                    });
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const monthIndex = Math.floor(index / 2);
                                    const genderIndex = index % 2;
                                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                    const gender = genderIndex === 0 ? 'Lelaki' : 'Perempuan';
                                    
                                    return `${months[monthIndex]} - ${gender}`;
                                },
                                label: function(context) {
                                    if (chartType === 'stacked') {
                                        return context.dataset.label + ': ' + context.parsed.y + ' patients';
                                    } else {
                                        const gender = context.dataset.label === 'Lelaki' ? 'Lelaki' : 'Perempuan';
                                        return gender + ': ' + context.parsed.y + ' patients';
                                    }
                                }
                            }
                        }
                    },
                    animation: {
                        onComplete: function() {
                            drawImprovedTwoLayerLabels(this, data);
                            // ADDED: Draw month separators after labels
                            drawMonthSeparators(this);
                        }
                    }
                }
            };

            chart = new Chart(ctx, config);
        }

        // UPDATED: Enhance existing grid lines to extend to month labels
        function drawMonthSeparators(chart) {
            const ctx = chart.ctx;
            const chartArea = chart.chartArea;
            
            ctx.save();
            ctx.strokeStyle = '#ddd'; // Change this color: '#ddd' = lighter, '#999' = darker, '#ccc' = medium
            ctx.lineWidth = 1;
            
            // Get the existing grid line positions after each month pair
            for (let monthIndex = 0; monthIndex < 11; monthIndex++) { // 11 separators between 12 months
                const perempuanIndex = monthIndex * 2 + 1; // Index of P bar for this month
                
                const meta0 = chart.getDatasetMeta(0);
                const meta1 = chart.getDatasetMeta(1);
                
                let rightBarX = null;
                let leftBarX = null;
                
                // Get the position between current month's P bar and next month's L bar
                if (currentChartType === 'stacked') {
                    if (meta0.data[perempuanIndex]) {
                        rightBarX = meta0.data[perempuanIndex].x;
                    }
                    if (meta0.data[perempuanIndex + 1]) {
                        leftBarX = meta0.data[perempuanIndex + 1].x;
                    }
                } else {
                    if (meta1.data[perempuanIndex]) {
                        rightBarX = meta1.data[perempuanIndex].x;
                    }
                    if (meta0.data[perempuanIndex + 1]) {
                        leftBarX = meta0.data[perempuanIndex + 1].x;
                    }
                }
                
                if (rightBarX !== null && leftBarX !== null) {
                    // Position the line exactly in the middle between the bars (where grid line naturally falls)
                    const lineX = (rightBarX + leftBarX) / 2;
                    
                    // Redraw the existing grid line darker and extend it down
                    ctx.beginPath();
                    ctx.moveTo(lineX, chartArea.top);
                    ctx.lineTo(lineX, chartArea.bottom + 50); // Extend down to month labels
                    ctx.stroke();
                }
            }
            
            ctx.restore();
        }

        // UNCHANGED: Existing label drawing function
        function drawImprovedTwoLayerLabels(chart, data) {
            const ctx = chart.ctx;
            const chartArea = chart.chartArea;
            
            ctx.save();
            ctx.textAlign = 'center';
            
            // FIXED: Better positioning to avoid overlap
            const genderLabelY = chartArea.bottom + 20;  // Gender labels (L/P) - closer to chart
            const monthLabelY = chartArea.bottom + 45;   // Month labels - further down
            
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            // Draw gender labels (L/P) - first layer
            ctx.font = '12px Arial';
            ctx.fillStyle = '#666';
            ctx.textBaseline = 'top';
            
            const labels = chart.data.labels;
            labels.forEach((label, index) => {
                const meta0 = chart.getDatasetMeta(0);
                
                if (currentChartType === 'stacked') {
                    // For stacked chart, each bar position represents both genders stacked
                    if (meta0.data[index]) {
                        ctx.fillText(label, meta0.data[index].x, genderLabelY);
                    }
                } else {
                    // For grouped chart, position L/P labels under the appropriate bars
                    const meta1 = chart.getDatasetMeta(1);
                    
                    if (label === 'L' && meta0.data[index]) {
                        ctx.fillText('L', meta0.data[index].x, genderLabelY);
                    } else if (label === 'P' && meta1.data[index]) {
                        ctx.fillText('P', meta1.data[index].x, genderLabelY);
                    }
                }
            });
            
            // Draw month labels - second layer (further down)
            ctx.font = '13px Arial';
            ctx.fillStyle = '#333';
            ctx.fontWeight = 'bold';
            
            for (let monthIndex = 0; monthIndex < 12; monthIndex++) {
                const monthLabel = months[monthIndex];
                const lelakiIndex = monthIndex * 2;
                const perempuanIndex = monthIndex * 2 + 1;
                
                const meta0 = chart.getDatasetMeta(0);
                const meta1 = chart.getDatasetMeta(1);
                
                if (currentChartType === 'stacked') {
                    // For stacked chart, center between L and P positions
                    const leftBar = meta0.data[lelakiIndex];
                    const rightBar = meta0.data[perempuanIndex];
                    
                    if (leftBar && rightBar) {
                        const centerX = (leftBar.x + rightBar.x) / 2;
                        ctx.fillText(monthLabel, centerX, monthLabelY);
                    } else if (leftBar) {
                        ctx.fillText(monthLabel, leftBar.x, monthLabelY);
                    }
                } else {
                    // For grouped chart, center between the two datasets for each month pair
                    const lelakiBar = meta0.data[lelakiIndex];
                    const perempuanBar = meta1.data[perempuanIndex];
                    
                    if (lelakiBar && perempuanBar) {
                        const centerX = (lelakiBar.x + perempuanBar.x) / 2;
                        ctx.fillText(monthLabel, centerX, monthLabelY);
                    } else if (lelakiBar) {
                        // If only Lelaki data exists, center it
                        ctx.fillText(monthLabel, lelakiBar.x, monthLabelY);
                    } else if (perempuanBar) {
                        // If only Perempuan data exists, center it
                        ctx.fillText(monthLabel, perempuanBar.x, monthLabelY);
                    }
                }
            }
            
            ctx.restore();
        }

        // Updated demo function to show the new format
        function demoUpdatedChart() {
            const sampleData = {
                labels: ['L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P', 'L', 'P'], // 24 labels for 12 months
                datasets: [
                    {
                        label: 'Lelaki',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0], // Data at L positions only
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Perempuan',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0], // Data at P positions only
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ],
                month_positions: [
                    {month: 'Jan', start_index: 0, end_index: 1},
                    {month: 'Feb', start_index: 2, end_index: 3},
                    {month: 'Mar', start_index: 4, end_index: 5},
                    {month: 'Apr', start_index: 6, end_index: 7},
                    {month: 'May', start_index: 8, end_index: 9},
                    {month: 'Jun', start_index: 10, end_index: 11},
                    {month: 'Jul', start_index: 12, end_index: 13},
                    {month: 'Aug', start_index: 14, end_index: 15},
                    {month: 'Sep', start_index: 16, end_index: 17},
                    {month: 'Oct', start_index: 18, end_index: 19},
                    {month: 'Nov', start_index: 20, end_index: 21},
                    {month: 'Dec', start_index: 22, end_index: 23}
                ]
            };
            
            currentChartType = 'grouped';
            renderChart(sampleData, 'grouped');
            updateChartTypeIndicator('grouped', 'Sample Data');
        }

        function showError(message) {
            const errorDiv = document.getElementById('error');
            errorDiv.innerHTML = message;
            errorDiv.style.display = 'block';
        }
        
        function showDebugInfo(info) {
            const debugDiv = document.getElementById('debug-info');
            debugDiv.textContent = JSON.stringify(info, null, 2);
            debugDiv.style.display = 'block';
        }

        // Table functions (keeping the existing functionality)
        function generateTable() {
    const bahagianUtama = document.getElementById('bahagian_utama').value;
    const month = document.getElementById('month').value;
    const year = document.getElementById('year').value;

    // Show loading
    document.getElementById('table-loading').style.display = 'block';
    document.getElementById('table-error').style.display = 'none';

    // Prepare form data
    const formData = new FormData();
    if (bahagianUtama) formData.append('bahagian_utama', bahagianUtama);
    if (month) formData.append('month', month);
    if (year) formData.append('year', year);

    fetch('get_table_data', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            document.getElementById('table-loading').style.display = 'none';
            
            if (data.status === 'success') {
                renderTable(data.data);
            } else {
                showTableError(data.message || 'Failed to load table data');
            }
        } catch (e) {
            document.getElementById('table-loading').style.display = 'none';
            showTableError('Invalid JSON response. Raw response: ' + text.substring(0, 500));
        }
    })
    .catch(error => {
        document.getElementById('table-loading').style.display = 'none';
        showTableError('Error loading table data: ' + error.message);
    });
}
        // Update the renderTable function:
        function renderTable(data) {
            const tableContent = document.getElementById('table-content');
            
            let tableHtml = `
                <div class="table-responsive">
                    <table class="table table-hierarchy">
                        <thead class="table-light">
                            <tr>
                                <th>Bahagian Utama</th>
                                <th>Sub Bahagian</th>
                                <th class="text-center">L</th>
                                <th class="text-center">P</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data && data.length > 0) {
                // Group data hierarchically: bahagian_utama -> sub_bahagian -> jantina
                const groupedData = {};
                let grandTotalLelaki = 0;
                let grandTotalPerempuan = 0;
                let grandTotal = 0;

                data.forEach(row => {
                    const bahagian = row.bahagian_utama || 'N/A';
                    const subBahagian = row.sub_bahagian || 'N/A';
                    
                    if (!groupedData[bahagian]) {
                        groupedData[bahagian] = {};
                    }
                    
                    if (!groupedData[bahagian][subBahagian]) {
                        groupedData[bahagian][subBahagian] = {
                            lelaki: 0,
                            perempuan: 0,
                            total: 0
                        };
                    }
                    
                    const count = parseInt(row.total) || 0;
                    if (row.jantina && row.jantina.toLowerCase() === 'lelaki') {
                        groupedData[bahagian][subBahagian].lelaki += count;
                        grandTotalLelaki += count;
                    } else if (row.jantina && row.jantina.toLowerCase() === 'perempuan') {
                        groupedData[bahagian][subBahagian].perempuan += count;
                        grandTotalPerempuan += count;
                    }
                    groupedData[bahagian][subBahagian].total += count;
                    grandTotal += count;
                });

                // Calculate totals for each bahagian utama and sort them
                const bahagianTotals = {};
                Object.keys(groupedData).forEach(bahagian => {
                    bahagianTotals[bahagian] = 0;
                    Object.keys(groupedData[bahagian]).forEach(subBahagian => {
                        bahagianTotals[bahagian] += groupedData[bahagian][subBahagian].total;
                    });
                });

                // Sort bahagian utama by total (descending) then alphabetically (ascending)
                const sortedBahagianKeys = Object.keys(groupedData).sort((a, b) => {
                    const totalA = bahagianTotals[a];
                    const totalB = bahagianTotals[b];
                    
                    // Primary sort: by total count (descending)
                    if (totalA !== totalB) {
                        return totalB - totalA;
                    }
                    
                    // Secondary sort: alphabetically (ascending)
                    return a.localeCompare(b);
                });

                // Add rows for each bahagian and sub_bahagian in sorted order
                sortedBahagianKeys.forEach(bahagian => {
                    const subBahagians = groupedData[bahagian];
                    const subBahagianKeys = Object.keys(subBahagians);
                    
                    // Determine the CSS class based on Bahagian Utama
                    let rowClass = '';
                    if (bahagian.includes('Skull') || bahagian.includes('Head')) {
                        rowClass = 'skull-head';
                    } else if (bahagian.includes('Spine')) {
                        rowClass = 'spine';
                    } else if (bahagian.includes('Chest')) {
                        rowClass = 'chest';
                    } else if (bahagian.includes('Abdomen')) {
                        rowClass = 'abdomen';
                    } else if (bahagian.includes('Upper Extremities')) {
                        rowClass = 'upper-extremities';
                    } else if (bahagian.includes('Lower Extremities')) {
                        rowClass = 'lower-extremities';
                    }
                    
                    // Add first row with bahagian and first sub-bahagian
                    if (subBahagianKeys.length > 0) {
                        const firstSubBahagian = subBahagianKeys[0];
                        const firstGroup = subBahagians[firstSubBahagian];
                        
                        tableHtml += `
                            <tr class="bahagian-header ${rowClass}">
                                <td class="bahagian-utama-cell">${bahagian}</td>
                                <td>${firstSubBahagian}</td>
                                <td class="text-center">${firstGroup.lelaki}</td>
                                <td class="text-center">${firstGroup.perempuan}</td>
                                <td class="text-center fw-bold">${firstGroup.total}</td>
                            </tr>
                        `;
                        
                        // Add remaining sub-bahagians
                        for (let i = 1; i < subBahagianKeys.length; i++) {
                            const subBahagian = subBahagianKeys[i];
                            const group = subBahagians[subBahagian];
                            
                            tableHtml += `
                                <tr class="sub-bahagian-row">
                                    <td></td>
                                    <td>${subBahagian}</td>
                                    <td class="text-center">${group.lelaki}</td>
                                    <td class="text-center">${group.perempuan}</td>
                                    <td class="text-center fw-bold">${group.total}</td>
                                </tr>
                            `;
                        }
                    } else {
                        // Case where bahagian has no sub-bahagians
                        tableHtml += `
                            <tr class="bahagian-header ${rowClass}">
                                <td class="bahagian-utama-cell">${bahagian}</td>
                                <td colspan="4" class="text-muted">No sub-bahagian</td>
                            </tr>
                        `;
                    }
                });

                // Add total row
                tableHtml += `
                    <tr class="grand-total-row">
                        <th colspan="2">Grand Total</th>
                        <th class="text-center">${grandTotalLelaki}</th>
                        <th class="text-center">${grandTotalPerempuan}</th>
                        <th class="text-center">${grandTotal}</th>
                    </tr>
                `;
            } else {
                tableHtml += `
                    <tr class="no-data-row">
                        <td colspan="5" class="text-center text-muted">No data available</td>
                    </tr>
                `;
            }

            tableHtml += `
                        </tbody>
                    </table>
                </div>
            `;

            tableContent.innerHTML = tableHtml;
        }

        function showTableError(message) {
            const errorDiv = document.getElementById('table-error');
            errorDiv.innerHTML = message;
            errorDiv.style.display = 'block';
        }

        function goBack() {
            window.history.back();
        }

        // Initialize with current year
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            
            // Initialize year selects for both graph and table
            const yearSelects = ['year', 'table_year'];
            
            yearSelects.forEach(selectId => {
                const yearSelect = document.getElementById(selectId);
                
                // Add current year if not already in the list
                const existingOption = Array.from(yearSelect.options).find(option => option.value == currentYear);
                if (!existingOption && currentYear > 2024) {
                    const option = new Option(currentYear, currentYear);
                    yearSelect.insertBefore(option, yearSelect.options[1]);
                }
            });
        });
    </script>
</body>
</html>