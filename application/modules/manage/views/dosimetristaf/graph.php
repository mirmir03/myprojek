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
            min-width: 120px;
        }

        .filter-btn {
            background: #007bff;
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
            background: #0056b3;
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
            height: 3px;
            border-radius: 2px;
        }

        .legend-color.green {
            background: #28a745;
        }

        .legend-color.blue {
            background: #007bff;
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

        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Graf Analisis Dosimetri Staf</h1>

        <!-- Filter Section - Year Only -->
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
                
                <button type="submit" class="filter-btn">Tapis Data</button>
            </form>
        </div>

        <!-- Combined Chart Section -->
        <div class="chart-section">
            <div class="chart-header">
                <div class="chart-title">Jumlah Dos Terkumpul</div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color green"></div>
                        <span>T04_DOS_AVE1</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color blue"></div>
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

        var combinedChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allLabels,
                datasets: [{
                    label: 'T04_DOS_AVE1',
                    data: data_ave1,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }, {
                    label: 'T04_DOS_AVE2',
                    data: data_ave2,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Hide default legend since we have custom legend
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        cornerRadius: 6,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
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
                },
                elements: {
                    line: {
                        fill: true
                    }
                }
            }
        });
    </script>
</body>
</html>