<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graf Analisis Dosimetri Staf</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
        }

        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 200px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 500;
            color: #495057;
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            width: 100%;
        }

        .filter-btn {
            background: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 20px;
        }

        .filter-btn:hover {
            background: #2e59d9;
        }

        .chart-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .legend {
            display: flex;
            gap: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #495057;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .legend-color.blue {
            background: #6c8ebf;
        }

        .legend-color.teal {
            background: #79a8b9;
        }

        .chart-container {
            position: relative;
            height: 400px;
            background: #fafafa;
            border-radius: 8px;
            padding: 20px;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .legend {
                flex-wrap: wrap;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Graf Analisis Dosimetri Staf</h1>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="<?php echo current_url(); ?>" class="filter-form">
                <div class="filter-group">
                    <label for="year">Tahun:</label>
                    <select name="year" id="year">
                        <option value="">Semua Tahun</option>
                        <?php foreach($years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo ($selected_year == $year) ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="staff">Staf:</label>
                    <select name="staff" id="staff">
                        <option value="">Semua Staf</option>
                        <?php foreach($staff_list as $staff): ?>
                            <option value="<?php echo $staff['id']; ?>" <?php echo ($selected_staff == $staff['id']) ? 'selected' : ''; ?>>
                                <?php echo $staff['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Tapis Data</button>
            </form>
        </div>

        <!-- Combined Chart Section -->
        <div class="chart-section">
            <div class="chart-header">
                <div class="chart-title">Jumlah Dos Terkumpul</div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color blue"></div>
                        <span>T04_DOS_AVE1</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color teal"></div>
                        <span>T04_DOS_AVE2</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="combinedChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Combined Chart
        var ctx = document.getElementById('combinedChart').getContext('2d');
        
        // Get the data
        var labels_ave1 = <?php echo $chart_labels_ave1; ?>;
        var values_ave1 = <?php echo $chart_values_ave1; ?>;
        var labels_ave2 = <?php echo $chart_labels_ave2; ?>;
        var values_ave2 = <?php echo $chart_values_ave2; ?>;
        
        // Create a combined labels array (use all unique labels)
        var allLabels = [...new Set([...labels_ave1, ...labels_ave2])];
        
        // Map data to combined labels
        var data_ave1 = allLabels.map(label => {
            var index = labels_ave1.indexOf(label);
            return index !== -1 ? values_ave1[index] : 0;
        });
        
        var data_ave2 = allLabels.map(label => {
            var index = labels_ave2.indexOf(label);
            return index !== -1 ? values_ave2[index] : 0;
        });

         // Global chart variable
    var combinedChart;

    function initializeOrUpdateChart() {
        // Get the data
        var labels_ave1 = <?php echo $chart_labels_ave1; ?>;
        var values_ave1 = <?php echo $chart_values_ave1; ?>;
        var labels_ave2 = <?php echo $chart_labels_ave2; ?>;
        var values_ave2 = <?php echo $chart_values_ave2; ?>;
        
        // Create a combined labels array
        var allLabels = [...new Set([...labels_ave1, ...labels_ave2])];
        
        // Map data to combined labels
        var data_ave1 = allLabels.map(label => {
            var index = labels_ave1.indexOf(label);
            return index !== -1 ? values_ave1[index] : 0;
        });
        
        var data_ave2 = allLabels.map(label => {
            var index = labels_ave2.indexOf(label);
            return index !== -1 ? values_ave2[index] : 0;
        });

        // Check if we have any data to display
        var hasData = data_ave1.some(val => val > 0) || data_ave2.some(val => val > 0);
        
        if (!hasData) {
            // Display a message if no data
            document.querySelector('.chart-container').innerHTML = 
                '<div class="no-data-message">Tiada data untuk paparan graf</div>';
            if (combinedChart) {
                combinedChart.destroy();
            }
            return;
        }

        // Get or create the canvas element
        var canvas = document.querySelector('#combinedChart');
        if (!canvas) {
            var container = document.querySelector('.chart-container');
            container.innerHTML = '<canvas id="combinedChart"></canvas>';
            canvas = document.querySelector('#combinedChart');
        }

        var ctx = canvas.getContext('2d');
        
        // Destroy existing chart if it exists
        if (combinedChart) {
            combinedChart.destroy();
        }

        // Create new chart
        combinedChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allLabels,
                datasets: [{
                    label: 'T04_DOS_AVE1',
                    data: data_ave1,
                    backgroundColor: 'rgba(108, 142, 191, 0.7)',
                    borderColor: 'rgba(108, 142, 191, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7
                }, {
                    label: 'T04_DOS_AVE2',
                    data: data_ave2,
                    backgroundColor: 'rgba(121, 168, 185, 0.7)',
                    borderColor: 'rgba(121, 168, 185, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 6,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toFixed(3);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d',
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        min: 0,
                        ticks: {
                            stepSize: 0.05,
                            color: '#6c757d',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value.toFixed(2);
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
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

    // Initialize the chart when the page loads
    document.addEventListener('DOMContentLoaded', initializeOrUpdateChart);
    </script>
</body>
</html>