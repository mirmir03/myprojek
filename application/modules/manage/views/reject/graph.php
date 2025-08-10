<!DOCTYPE html>
<html>
<head>
    <title>Graf Analisis Reject</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .filter-container {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            min-width: 120px;
        }
        
        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }
        
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn {
            padding: 8px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            height: fit-content;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .chart-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            color: #343a40;
        }
        
        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        
        .canvas-container {
            position: relative;
            height: 500px;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .stats-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Graf Analisis Reject</h2>
        
        <!-- Filter Section -->
        <div class="filter-container">
            <form method="GET" id="filterForm">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="month">Bulan:</label>
                        <select name="month" id="month">
                            <option value="">Semua Bulan</option>
                            <option value="1" <?= ($selected_month == '1') ? 'selected' : '' ?>>Januari</option>
                            <option value="2" <?= ($selected_month == '2') ? 'selected' : '' ?>>Februari</option>
                            <option value="3" <?= ($selected_month == '3') ? 'selected' : '' ?>>Mac</option>
                            <option value="4" <?= ($selected_month == '4') ? 'selected' : '' ?>>April</option>
                            <option value="5" <?= ($selected_month == '5') ? 'selected' : '' ?>>Mei</option>
                            <option value="6" <?= ($selected_month == '6') ? 'selected' : '' ?>>Jun</option>
                            <option value="7" <?= ($selected_month == '7') ? 'selected' : '' ?>>Julai</option>
                            <option value="8" <?= ($selected_month == '8') ? 'selected' : '' ?>>Ogos</option>
                            <option value="9" <?= ($selected_month == '9') ? 'selected' : '' ?>>September</option>
                            <option value="10" <?= ($selected_month == '10') ? 'selected' : '' ?>>Oktober</option>
                            <option value="11" <?= ($selected_month == '11') ? 'selected' : '' ?>>November</option>
                            <option value="12" <?= ($selected_month == '12') ? 'selected' : '' ?>>Disember</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="year">Tahun:</label>
                        <select name="year" id="year">
                            <option value="">Semua Tahun</option>
                            <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                                <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Tapis</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                </div>
            </form>
        </div>
        
        <div class="row">
    <!-- Left side: Chart Section -->
    <div class="col-md-8">
        <div class="chart-container">
            <div class="chart-title">
                Graf Jumlah Reject Mengikut Jenis
                <?php if (!empty($selected_month) || !empty($selected_year)): ?>
                    <br><small style="font-weight: normal; color: #6c757d;">
                        <?php 
                        $filter_text = [];
                        if (!empty($selected_month)) {
                            $months = ['', 'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 
                                        'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
                            $filter_text[] = $months[$selected_month];
                        }
                        if (!empty($selected_year)) {
                            $filter_text[] = $selected_year;
                        }
                        echo implode(' ', $filter_text);
                        ?>
                    </small>
                <?php endif; ?>
            </div>
            <div id="chartContent">
                <?php if (!empty($chart_labels) && json_decode($chart_labels)): ?>
                    <div class="stats-container">
                        <div class="stat-item">
                            <div class="stat-label">Jumlah Reject</div>
                            <div class="stat-value" id="totalRejects">0</div>
                            
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Jenis Reject</div>
                            <div class="stat-value" id="totalTypes">0</div>
                            
                        </div>
                    </div>
                    <div class="canvas-container">
                        <canvas id="rejectChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <h4>Tiada Data</h4>
                        <p>Tiada data reject dijumpai untuk tapisan terpilih.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right side: Notes Section -->
    <div class="col-md-4">
        <?php $this->load->view('manage/reject/notes_management'); ?>
    </div>
</div>


    <script>
        // Chart data from PHP
        const chartLabels = <?= $chart_labels ?? '[]' ?>;
        const chartValues = <?= $chart_values ?? '[]' ?>;
        
        // Initialize chart if data exists
        if (chartLabels.length > 0) {
            initializeChart();
            updateStats();
        }
        
        function initializeChart() {
            const ctx = document.getElementById('rejectChart').getContext('2d');
            
            // Generate colors for each bar
            const colors = generateColors(chartLabels.length);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Reject',
                        data: chartValues,
                        backgroundColor: colors.background,
                        borderColor: colors.border,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // This makes it horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.x + ' reject';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Reject'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Jenis Reject'
                            }
                        }
                    }
                }
            });
        }
        
        function generateColors(count) {
            const colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
            ];
            
            const background = [];
            const border = [];
            
            for (let i = 0; i < count; i++) {
                const color = colors[i % colors.length];
                background.push(color + '80'); // Add transparency
                border.push(color);
            }
            
            return { background, border };
        }
        
        function updateStats() {
            const total = chartValues.reduce((sum, val) => sum + val, 0);
            const types = chartLabels.length;
            
            document.getElementById('totalRejects').textContent = total;
            document.getElementById('totalTypes').textContent = types;
        }
        
        function resetFilters() {
            document.getElementById('month').value = '';
            document.getElementById('year').value = '';
            document.getElementById('filterForm').submit();
        }
        
        // Auto-submit form when filters change
        document.getElementById('month').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        
        document.getElementById('year').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
</body>
</html>