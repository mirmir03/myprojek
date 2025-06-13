<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Statistics Graph</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .filter-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .filter-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            align-self: flex-end;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn.debug {
            background: #28a745;
            margin-top: 5px;
        }
        .btn.debug:hover {
            background: #1e7e34;
        }
        .chart-container {
            position: relative;
            height: 500px;
            margin-top: 20px;
        }
        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .error {
            color: #dc3545;
            text-align: center;
            padding: 20px;
            background: #f8d7da;
            border-radius: 4px;
            margin: 20px 0;
        }
        .debug-info {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Statistik Pesakit X-ray</h1>
            <p>View patient distribution by gender across different sub-departments</p>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="bahagian_utama">Bahagian Utama *</label>
                <select id="bahagian_utama" required>
                    <option value="">Select Bahagian Utama</option>
                    <option value="Skull and Head">Skull and Head</option>
                    <option value="Spine">Spine</option>
                    <option value="Chest">Chest</option>
                    <option value="Abdomen">Abdomen</option>
                    <option value="Upper Extremities">Upper Extremities</option>
                    <option value="Lower Extremities">Lower Extremities</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="kategori">Kategori *</label>
                <select id="kategori" required>
                    <option value="">Select Kategori</option>
                    <option value="pelajar">Pelajar</option>
                    <option value="staf">Staf</option>
                    <option value="pesara">Pesara</option>
                    <option value="tanggungan">Tanggungan</option>
                    <option value="warga luar">Warga Luar</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="month">Month (Optional)</label>
                <select id="month">
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

            <div class="filter-group">
                <label for="year">Year (Optional)</label>
                <select id="year">
                    <option value="">All Years</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <option value="2021">2021</option>
                    <option value="2020">2020</option>
                </select>
            </div>

            <div class="filter-group">
                <button class="btn" onclick="generateGraph()">Generate Graph</button>
                <button class="btn debug" onclick="debugData()">Debug Data</button>
            </div>
        </div>

        <div id="loading" class="loading" style="display: none;">
            Loading chart data...
        </div>

        <div id="error" class="error" style="display: none;"></div>
        
        <div id="debug-info" class="debug-info" style="display: none;"></div>

        <div class="chart-container">
            <canvas id="patientChart"></canvas>
        </div>
    </div>

    <script>
        let chart = null;
        
        // UPDATED: Define the base URL - adjust this to match your CodeIgniter setup
        const BASE_URL = window.location.origin + window.location.pathname.replace('/patient_graph', '');

        function generateGraph() {
            const bahagianUtama = document.getElementById('bahagian_utama').value;
            const kategori = document.getElementById('kategori').value;
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;

            // Validate required fields
            if (!bahagianUtama || !kategori) {
                showError('Please select both Bahagian Utama and Kategori');
                return;
            }

            // Show loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            document.getElementById('debug-info').style.display = 'none';

            // Prepare form data
            const formData = new FormData();
            formData.append('bahagian_utama', bahagianUtama);
            formData.append('kategori', kategori);
            if (month) formData.append('month', month);
            if (year) formData.append('year', year);

            // UPDATED: Use relative URL that should work with most CodeIgniter setups
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
                        renderChart(data.data);
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

        function renderChart(data) {
            const ctx = document.getElementById('patientChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (chart) {
                chart.destroy();
            }

            chart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Patients'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Sub Bahagian'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Patient Distribution by Gender and Sub Bahagian',
                            font: {
                                size: 16
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' patients';
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
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

        // UPDATED: Debug function with better error handling
        function debugData() {
            const bahagianUtama = document.getElementById('bahagian_utama').value;
            const kategori = document.getElementById('kategori').value;

            if (!bahagianUtama || !kategori) {
                showError('Please select both Bahagian Utama and Kategori for debugging');
                return;
            }

            const formData = new FormData();
            formData.append('bahagian_utama', bahagianUtama);
            formData.append('kategori', kategori);

            const url = 'debug_graph_data';
            
            console.log('Making debug request to:', url);

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                console.log('Debug raw response:', text);
                
                try {
                    const data = JSON.parse(text);
                    console.log('Debug parsed data:', data);
                    showDebugInfo(data);
                    
                    if (data.status === 'success') {
                        alert('Debug data displayed below the form. Check console for detailed logs.');
                    } else {
                        showError('Debug error: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Debug JSON parse error:', e);
                    showError('Debug response is not valid JSON: ' + text.substring(0, 500));
                }
            })
            .catch(error => {
                console.error('Debug fetch error:', error);
                showError('Debug request failed: ' + error.message);
            });
        }

        // Initialize with current year
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            const yearSelect = document.getElementById('year');
            
            // Add current year if not already in the list
            const existingOption = Array.from(yearSelect.options).find(option => option.value == currentYear);
            if (!existingOption && currentYear > 2024) {
                const option = new Option(currentYear, currentYear);
                yearSelect.insertBefore(option, yearSelect.options[1]);
            }
        });
    </script>
</body>
</html>