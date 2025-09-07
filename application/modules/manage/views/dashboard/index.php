<!-- Add Font Awesome CSS in your head section -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  /* Your CSS unchanged */
  .dashboard-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }
  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
  }
  .dashboard-card .card-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.8;
    transition: all 0.3s ease;
  }
  .dashboard-card:hover .card-icon {
    transform: scale(1.1);
    opacity: 1;
  }
  .card-bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #1abc9c 100%);
  }
  .card-bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
  }
  .card-bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
  }
  .card-value {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }
  .card-label {
    letter-spacing: 0.5px;
    font-size: 1rem;
  }
  .card-footer-text {
    font-size: 0.75rem;
    opacity: 0.8;
  }
</style>

<div class="row justify-content-center">
  <!-- Active Patients Card -->
  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="dashboard-card card-bg-info h-100 text-white">
      <div class="card-body text-center d-flex flex-column justify-content-center p-4">
        <i class="fas fa-user-injured card-icon"></i>
        <h2 id="active-patient-count" class="card-value"><?= $total_active_patients; ?></h2>
        <h6 class="card-label mb-2">Active Patients</h6>
        <small class="card-footer-text">Updated just now</small>
      </div>
    </div>
  </div>
  
  <!-- Total Rejects Card -->
  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="dashboard-card card-bg-secondary h-100 text-white">
      <div class="card-body text-center d-flex flex-column justify-content-center p-4">
        <i class="fas fa-times-circle card-icon"></i>
        <h2 id="total-rejects-count" class="card-value"><?= $total_rejects_count; ?></h2>
        <h6 class="card-label mb-2">Total Rejects</h6>
        <small class="card-footer-text">All records</small>
      </div>
    </div>
  </div>
  
  <!-- Pending Notifications Card -->
  <div class="col-sm-6 col-lg-3 mb-4">
    <div class="dashboard-card card-bg-warning h-100 text-white">
      <div class="card-body text-center d-flex flex-column justify-content-center p-4">
        <i class="fas fa-bell card-icon"></i>
        <h2 id="total-unreceived-notifikasi" class="card-value"><?= $total_unreceived_notifikasi; ?></h2>
        <h6 class="card-label mb-2">Pending Notifications</h6>
        <small class="card-footer-text">Requires attention</small>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <!-- Active Patients Chart -->
  <div class="col-lg-6 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body">
        <h4 class="card-title">Active Patients by Month (<?= date('Y') ?>)</h4>
        <div class="mt-4">
          <div id="activePatientsChart" style="height: 300px;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Reject Analysis Chart -->
  <div class="col-lg-6 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body">
        <div class="row mt-4">
          <div class="col-md-7">
            <div id="reject-analysis-chart" style="min-height: 250px;"></div>
          </div>
          <div class="col-md-5 align-self-center">
            <h1 class="mb-0"><?= $current_year_rejects; ?></h1>
            <h6 class="text-muted"><?= date('Y') ?> Rejects</h6>
            <ul class="list-icons mt-4 list-style-none" id="reject-legend"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <!-- First All Bahagian Utama Chart -->
  <div class="col-lg-6 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body">
        <h4 class="card-title">All Bahagian Utama by Month (<?= $current_year ?>)</h4>
        <canvas id="allBahagianChart1" style="height: 350px;"></canvas>
      </div>
    </div>
  </div>

  <!-- Second All Bahagian Utama Chart -->
  <div class="col-lg-6 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body">
        <h4 class="card-title">All Bahagian Utama by Month (<?= $current_year ?>) - Duplicate</h4>
        <canvas id="allBahagianChart2" style="height: 350px;"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  // Auto refresh counts
  setInterval(updateActivePatientCount, 5000);
  setInterval(updateRejectCount, 5000);
  setInterval(updateUnreceivedNotifikasi, 5000);

  // Active Patients Area Chart with ApexCharts
  var chartData1 = <?= $active_patients_chart ?>;
  var series = chartData1.datasets.map(dataset => ({
    name: dataset.label,
    data: dataset.data
  }));

  var optionsActivePatients = {
    chart: {
      type: 'area',
      height: 300,
      toolbar: { show: false },
      zoom: { enabled: true }
    },
    series: series,
    xaxis: {
      categories: chartData1.labels,
      labels: { style: { colors: '#a1aab2', fontSize: '12px' } },
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: { labels: { style: { colors: '#a1aab2', fontSize: '12px' } } },
    grid: {
      show: true,
      borderColor: 'var(--bs-border-color)',
      strokeDashArray: 4,
      position: 'back'
    },
    stroke: { curve: 'smooth', width: 2 },
    colors: ['#17a2b8', '#28a745', '#ffc107', '#dc3545'],
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'light',
        type: 'vertical',
        shadeIntensity: 0.5,
        gradientToColors: [
          'rgba(23, 162, 184, 0.1)',
          'rgba(40, 167, 69, 0.1)',
          'rgba(255, 193, 7, 0.1)',
          'rgba(220, 53, 69, 0.1)'
        ],
        inverseColors: false,
        opacityFrom: 0.4,
        opacityTo: 0.1,
        stops: [0, 90, 100]
      }
    },
    dataLabels: { enabled: false },
    legend: { show: true, position: 'top', labels: { colors: '#666' } },
    tooltip: { theme: 'dark' }
  };
  var activePatientsChart = new ApexCharts(document.querySelector("#activePatientsChart"), optionsActivePatients);
  activePatientsChart.render();

  // Reject Analysis Donut Chart
  var rejectData = <?= $reject_chart_data ?>;
  var rejectColors = ['#6c757d', '#0d6efd', '#198754', '#ffc107', '#dc3545'];
  var optionsReject = {
    chart: { type: 'donut', height: 250 },
    series: rejectData.datasets[0].data.map(Number),
    labels: rejectData.labels,
    colors: rejectColors,
    legend: { show: false },
    dataLabels: { enabled: true },
    plotOptions: {
      pie: {
        donut: {
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Current Year',
              formatter: () => <?= $current_year_rejects ?>
            }
          }
        }
      }
    }
  };
  var rejectChart = new ApexCharts(document.querySelector("#reject-analysis-chart"), optionsReject);
  rejectChart.render();

  // All Bahagian Utama combined bar charts using Chart.js
  var allBahagianChartData = <?= $all_bahagian_chart ?? '{}' ?>;
  if (allBahagianChartData && allBahagianChartData.labels) {
    var colors = ['#1abc9c', '#17a2b8', '#ffc107', '#dc3545', '#28a745', '#6f42c1'];

    // Dataset for chart 1
    var datasets1 = allBahagianChartData.datasets.map((ds, i) => ({
      ...ds,
      label: ds.label || 'Series ' + (i + 1),
      backgroundColor: colors[i % colors.length],
      borderColor: colors[i % colors.length],
      borderWidth: 1,
    }));

    // Dataset for chart 2 with shifted colors
    var datasets2 = allBahagianChartData.datasets.map((ds, i) => ({
      ...ds,
      label: ds.label || 'Series ' + (i + 1),
      backgroundColor: colors[(i + 3) % colors.length],
      borderColor: colors[(i + 3) % colors.length],
      borderWidth: 1,
    }));

    // Chart 1
    var ctx1 = document.getElementById('allBahagianChart1').getContext('2d');
    new Chart(ctx1, {
      type: 'bar',
      data: { labels: allBahagianChartData.labels, datasets: datasets1 },
      options: {
        responsive: true,
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
          x: { stacked: true, title: { display: true, text: 'Month' } },
          y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Count' } }
        },
        plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
      }
    });

    // Chart 2
    var ctx2 = document.getElementById('allBahagianChart2').getContext('2d');
    new Chart(ctx2, {
      type: 'bar',
      data: { labels: allBahagianChartData.labels, datasets: datasets2 },
      options: {
        responsive: true,
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
          x: { stacked: true, title: { display: true, text: 'Month' } },
          y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Count' } }
        },
        plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
      }
    });
  }
});

function updateActivePatientCount() {
  fetch("<?= site_url('manage/dashboard/get_active_patient_count'); ?>")
    .then(res => res.json())
    .then(data => document.getElementById('active-patient-count').innerText = data.count);
}

function updateRejectCount() {
  fetch("<?= site_url('manage/dashboard/get_total_rejects_count'); ?>")
    .then(res => res.json())
    .then(data => {
      document.getElementById('total-rejects-count').innerText = data.count;
      if (typeof rejectChart !== 'undefined') {
        rejectChart.updateOptions({
          plotOptions: {
            pie: {
              donut: {
                labels: { total: { formatter: () => data.count } }
              }
            }
          }
        });
      }
    });
}

function updateUnreceivedNotifikasi() {
  fetch("<?= site_url('manage/dashboard/get_total_unreceived_notifikasi'); ?>")
    .then(res => res.json())
    .then(data => document.getElementById('total-unreceived-notifikasi').innerText = data.count);
}

// Legend building (reject chart)
var legendContainer = document.getElementById('reject-legend');
if (legendContainer) {
  legendContainer.style.display = 'flex';
  legendContainer.style.gap = '10px';
  legendContainer.style.listStyle = 'none';
  legendContainer.style.padding = '0';
  legendContainer.style.margin = '0';
  legendContainer.style.maxHeight = '200px';
  legendContainer.style.overflowY = 'auto';

  var half = Math.ceil(rejectData.labels.length / 2);
  var col1 = rejectData.labels.slice(0, half);
  var col2 = rejectData.labels.slice(half);

  function createLegendList(labels, colorOffset) {
    var ul = document.createElement('ul');
    ul.style.listStyle = 'none';
    ul.style.padding = '0';
    ul.style.margin = '0';
    ul.style.flex = '1';
    ul.style.minWidth = '0'; // Allow shrinking

    labels.forEach((label, i) => {
      var li = document.createElement('li');
      li.className = "d-flex align-items-center mb-2";
      li.style.fontSize = '12px';
      li.style.lineHeight = '1.3';
      li.innerHTML = `
        <i class="fa fa-circle me-2" style="color:${rejectColors[(i + colorOffset) % rejectColors.length]}; font-size: 8px; flex-shrink: 0;"></i>
        <span style="word-break: break-word; overflow-wrap: break-word; hyphens: auto;">${label}</span>
      `;
      ul.appendChild(li);
    });
    return ul;
  }

  legendContainer.appendChild(createLegendList(col1, 0));
  legendContainer.appendChild(createLegendList(col2, half));
}
</script>
