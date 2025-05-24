<div class="dashboard-container">
    <!-- Filter Section -->
    <div class="filter-card">
        <h3><i class="fas fa-filter"></i> Patient Filters</h3>
        <form id="statsFilter">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Month</label>
                    <select name="month" class="modern-select">
                        <option value="">All Months</option>
                        <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= sprintf("%02d", $i) ?>">
                            <?= date("F", mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category" class="modern-select">
                        <option value="">All Categories</option>
                        <option value="pelajar">Student</option>
                        <option value="staf">Staff</option>
                        <option value="warga luar">External</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Diagnosis Area</label>
                    <select name="diagnosis_area" class="modern-select">
                        <option value="">All Areas</option>
                        <option value="Abdomen">Abdomen</option>
                        <option value="Chest">Chest</option>
                        <option value="Spine">Spine</option>
                    </select>
                </div>
                
                <button type="submit" class="filter-button">
                    <i class="fas fa-chart-bar"></i> Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Visualization Section -->
    <div class="visualization-grid">
        <!-- Gender Distribution -->
        <div class="chart-card">
            <div class="chart-header">
                <h4><i class="fas fa-venus-mars"></i> Gender Distribution</h4>
                <div class="chart-legend">
                    <span class="legend-female"><i class="fas fa-circle"></i> Female</span>
                    <span class="legend-male"><i class="fas fa-circle"></i> Male</span>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="chart-summary" id="genderSummary">
                Loading data...
            </div>
        </div>

        <!-- Diagnosis by Gender -->
        <div class="chart-card">
            <div class="chart-header">
                <h4><i class="fas fa-procedures"></i> Diagnosis by Gender</h4>
                <div class="chart-legend">
                    <span class="legend-female"><i class="fas fa-square"></i> Female</span>
                    <span class="legend-male"><i class="fas fa-square"></i> Male</span>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="diagnosisChart"></canvas>
            </div>
            <div class="chart-summary">
                <div id="diagnosisSummary"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js with Animation Plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<style>
/* Modern Dashboard CSS */
.dashboard-container {
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.filter-card {
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 20px;
    margin-bottom: 25px;
}

.filter-card h3 {
    color: #3f51b5;
    margin-top: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    align-items: end;
}

.modern-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background-color: #f9f9f9;
    transition: all 0.3s;
}

.modern-select:focus {
    border-color: #3f51b5;
    box-shadow: 0 0 0 2px rgba(63,81,181,0.2);
    outline: none;
}

.filter-button {
    background: linear-gradient(135deg, #3f51b5, #2196f3);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.filter-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.visualization-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .visualization-grid {
        grid-template-columns: 1fr;
    }
}

.chart-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 20px;
    transition: all 0.3s;
}

.chart-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.chart-header h4 {
    margin: 0;
    color: #424242;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-legend {
    display: flex;
    gap: 15px;
}

.legend-female { color: #ff6384; }
.legend-male { color: #36a2eb; }

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-summary {
    margin-top: 15px;
    padding: 12px;
    background: #f5f5f5;
    border-radius: 6px;
    font-size: 14px;
}
</style>

<script>
$(document).ready(function() {
    // Initialize charts with empty data
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const diagnosisCtx = document.getElementById('diagnosisChart').getContext('2d');
    
    const genderChart = new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: ['Female', 'Male'],
            datasets: [{
                data: [0, 0],
                backgroundColor: ['#ff6384', '#36a2eb'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.raw} patients (${context.parsed}%)`;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold' },
                    formatter: (value) => value > 0 ? value : ''
                }
            },
            cutout: '70%',
            animation: {
                animateScale: true,
                animateRotate: true
            }
        },
        plugins: [ChartDataLabels]
    });

    const diagnosisChart = new Chart(diagnosisCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Female',
                    backgroundColor: '#ff6384',
                    data: [],
                    borderRadius: 4,
                    borderSkipped: false
                },
                {
                    label: 'Male',
                    backgroundColor: '#36a2eb',
                    data: [],
                    borderRadius: 4,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true },
                y: { 
                    stacked: true,
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw} patients`;
                        }
                    }
                },
                legend: { display: false }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });

    // Load initial data
    fetchData();

    // Filter form submission
    $('#statsFilter').on('submit', function(e) {
        e.preventDefault();
        fetchData();
    });

    // Fetch data from server
    function fetchData() {
        $('.filter-button').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        $.ajax({
            url: '<?= module_url("pesakit/get_filtered_data") ?>',
            type: 'POST',
            data: $('#statsFilter').serialize(),
            dataType: 'json',
            success: function(response) {
                updateCharts(response);
                $('.filter-button').html('<i class="fas fa-chart-bar"></i> Generate Report');
            },
            error: function() {
                alert('Error loading data');
                $('.filter-button').html('<i class="fas fa-chart-bar"></i> Generate Report');
            }
        });
    }

    // Update charts with new data
    function updateCharts(data) {
        // Process gender data
        const femaleCount = data.gender.find(item => item.gender === 'Perempuan')?.count || 0;
        const maleCount = data.gender.find(item => item.gender === 'Lelaki')?.count || 0;
        const totalPatients = femaleCount + maleCount;
        
        // Update gender chart
        genderChart.data.datasets[0].data = [femaleCount, maleCount];
        genderChart.update();
        
        // Update gender summary
        const femalePercent = totalPatients > 0 ? Math.round((femaleCount / totalPatients) * 100) : 0;
        const malePercent = totalPatients > 0 ? Math.round((maleCount / totalPatients) * 100) : 0;
        
        $('#genderSummary').html(`
            <strong>Total Patients:</strong> ${totalPatients}<br>
            <span class="legend-female"><i class="fas fa-circle"></i> Female:</span> ${femaleCount} (${femalePercent}%)<br>
            <span class="legend-male"><i class="fas fa-circle"></i> Male:</span> ${maleCount} (${malePercent}%)
        `);
        
        // Process diagnosis data
        const subParts = [...new Set(data.diagnosis.map(item => item.sub_part))];
        const femaleDiagnosis = [];
        const maleDiagnosis = [];
        
        subParts.forEach(subPart => {
            const female = data.diagnosis.find(item => 
                item.sub_part === subPart && item.gender === 'Perempuan'
            )?.count || 0;
            
            const male = data.diagnosis.find(item => 
                item.sub_part === subPart && item.gender === 'Lelaki'
            )?.count || 0;
            
            femaleDiagnosis.push(female);
            maleDiagnosis.push(male);
        });
        
        // Update diagnosis chart
        diagnosisChart.data.labels = subParts;
        diagnosisChart.data.datasets[0].data = femaleDiagnosis;
        diagnosisChart.data.datasets[1].data = maleDiagnosis;
        diagnosisChart.update();
        
        // Update diagnosis summary
        let summaryHTML = `<strong>Diagnosis Breakdown:</strong><ul>`;
        subParts.forEach((subPart, index) => {
            const fCount = femaleDiagnosis[index];
            const mCount = maleDiagnosis[index];
            const total = fCount + mCount;
            
            if (total > 0) {
                summaryHTML += `
                <li>
                    <strong>${subPart}:</strong> ${total} total
                    (<span class="legend-female">${fCount} F</span>,
                    <span class="legend-male">${mCount} M</span>)
                </li>`;
            }
        });
        summaryHTML += `</ul>`;
        $('#diagnosisSummary').html(summaryHTML);
    }
});
</script>