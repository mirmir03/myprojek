<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Laporan Pesakit
                        <button id="refreshData" class="btn btn-outline-info float-right">
                            <i class="fa fa-refresh"></i> Refresh Data
                        </button>
                    </h4>
                    
                    <!-- Filter Controls -->
                    <div class="filter-controls mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Filter Options</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="date" id="startDate" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="date" id="endDate" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Kategori</label>
                                            <select id="kategori" class="form-control">
                                                <option value="">All Categories</option>
                                                <option value="pelajar">Pelajar</option>
                                                <option value="staf">Staf</option>
                                                <option value="pesara">Pesara</option>
                                                <option value="tanggungan">Tanggungan</option>
                                                <option value="warga luar">Warga Luar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <button id="applyFilters" class="btn btn-primary">Apply Filters</button>
                                        <button id="resetFilters" class="btn btn-secondary ml-2">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart Container -->
                    <div id="category-data" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include needed JavaScript libraries -->
<link href="<?php echo base_url('assets/css/c3.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/js/d3.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/c3.min.js'); ?>"></script>

<script>
// Track library loading status
let librariesReady = false;

// Debug library loading
console.log('Initial check - D3:', typeof d3 !== 'undefined' ? 'Loaded' : 'Not loaded');
console.log('Initial check - C3:', typeof c3 !== 'undefined' ? 'Loaded' : 'Not loaded');

// Load libraries from CDN if local ones are not available
function loadLibrariesFromCDN() {
    console.warn('Loading libraries from CDN...');
    
    const loadScript = function(url, callback) {
        const script = document.createElement('script');
        script.src = url;
        script.onload = callback;
        document.head.appendChild(script);
    };
    
    const loadCSS = function(url) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = url;
        document.head.appendChild(link);
    };
    
    // Load D3 first, then C3
    loadCSS('https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.css');
    loadScript('https://cdnjs.cloudflare.com/ajax/libs/d3/5.16.0/d3.min.js', function() {
        console.log('D3 loaded from CDN');
        loadScript('https://cdnjs.cloudflare.com/ajax/libs/c3/0.7.20/c3.min.js', function() {
            console.log('C3 loaded from CDN');
            librariesReady = true;
            initializeApp();
        });
    });
}

// Verify if libraries are loaded and handle fallbacks
function checkLibrariesAndInitialize() {
    if (typeof d3 !== 'undefined' && typeof c3 !== 'undefined') {
        console.log('All libraries loaded successfully from local sources');
        librariesReady = true;
        initializeApp();
    } else {
        console.warn('Libraries not loaded from local paths, trying CDN fallback...');
        loadLibrariesFromCDN();
    }
}

// Main initialization function that's called when libraries are ready
function initializeApp() {
    setupEventListeners();
    fetchAndGenerateChart();
}

// Setup event listeners for filters and buttons
function setupEventListeners() {
    document.getElementById('applyFilters').addEventListener('click', function() {
        const filterOptions = {
            startDate: document.getElementById('startDate').value,
            endDate: document.getElementById('endDate').value,
            kategori: document.getElementById('kategori').value
        };
        
        if (window.currentChart) {
            window.currentChart.destroy();
        }
        fetchAndGenerateChart(filterOptions);
    });
    
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('kategori').value = '';
        
        if (window.currentChart) {
            window.currentChart.destroy();
        }
        fetchAndGenerateChart();
    });
    
    document.getElementById('refreshData').addEventListener('click', function() {
        if (window.currentChart) {
            window.currentChart.destroy();
        }
        fetchAndGenerateChart();
    });
}

function fetchAndGenerateChart(filterOptions = {}) {
    document.getElementById('category-data').innerHTML = '<div class="text-center p-5"><h4>Loading data...</h4></div>';
    
    let params = new URLSearchParams();
    params.append('fetch_data', 'true');
    
    if (filterOptions.startDate) params.append('startDate', filterOptions.startDate);
    if (filterOptions.endDate) params.append('endDate', filterOptions.endDate);
    if (filterOptions.kategori) params.append('kategori', filterOptions.kategori);
    
    fetch('<?php echo module_url("pesakit/graphs"); ?>?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data);
        
        if (data && typeof data === 'object' && 'status' in data) {
            if (data.status === 'empty') {
                document.getElementById('category-data').innerHTML = 
                    '<div class="alert alert-warning">No data available for the selected filters.</div>';
                return;
            } else if (data.status === 'error') {
                throw new Error(data.message || 'Unknown error');
            }
        }
        
        window.currentData = data;
        
        if (Array.isArray(data) && data.length === 0) {
            document.getElementById('category-data').innerHTML = 
                '<div class="alert alert-warning">No data available for the selected filters.</div>';
            return;
        }
        
        window.currentChart = generateChart(data, filterOptions);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        document.getElementById('category-data').innerHTML = 
            '<div class="alert alert-danger">Error loading data: ' + error.message + '. Please try again later.</div>';
    });
}

function generateChart(data, filterOptions = {}) {
    try {
        if (!data || data.length === 0) {
            document.getElementById('category-data').innerHTML = 
                '<div class="alert alert-warning">No data available for the selected filters.</div>';
            return null;
        }

        // Group data by BAHAGIAN_UTAMA and JANTINA
        const bahagianUtamas = [...new Set(data.map(item => item.T01_BAHAGIAN_UTAMA))].sort();
        
        const maleData = ['Lelaki'];
        const femaleData = ['Perempuan'];
        
        bahagianUtamas.forEach(bahagian => {
            const maleCount = data.filter(d => 
                d.T01_JANTINA.toUpperCase() === 'LELAKI' && 
                d.T01_BAHAGIAN_UTAMA === bahagian
            ).length;
            
            const femaleCount = data.filter(d => 
                d.T01_JANTINA.toUpperCase() === 'PEREMPUAN' && 
                d.T01_BAHAGIAN_UTAMA === bahagian
            ).length;
            
            maleData.push(maleCount);
            femaleData.push(femaleCount);
        });

        // Build the chart title based on filters
        let chartTitle = 'Taburan Pesakit Mengikut Jantina dan Bahagian Utama';
        if (filterOptions.kategori) {
            chartTitle += ` (${filterOptions.kategori.charAt(0).toUpperCase() + filterOptions.kategori.slice(1)})`;
        }
        if (filterOptions.startDate && filterOptions.endDate) {
            chartTitle += ` (${filterOptions.startDate} hingga ${filterOptions.endDate})`;
        } else if (filterOptions.startDate) {
            chartTitle += ` (dari ${filterOptions.startDate})`;
        } else if (filterOptions.endDate) {
            chartTitle += ` (hingga ${filterOptions.endDate})`;
        }

        const chart = c3.generate({
            bindto: '#category-data',
            data: {
                columns: [
                    maleData,
                    femaleData
                ],
                type: 'bar',
                colors: {
                    'Lelaki': '#4ECDC4',
                    'Perempuan': '#FF6B6B'
                }
            },
            title: {
                text: chartTitle
            },
            axis: {
                x: {
                    type: 'category',
                    categories: bahagianUtamas,
                    tick: {
                        rotate: 30,
                        multiline: false
                    },
                    height: 80
                },
                y: {
                    label: 'Bilangan Pesakit',
                    min: 0,
                    padding: {
                        bottom: 0
                    }
                }
            },
            bar: {
                width: {
                    ratio: 0.7
                }
            },
            grid: {
                y: {
                    show: true
                }
            },
            tooltip: {
                format: {
                    value: function(value) {
                        return value + ' pesakit';
                    }
                }
            },
            legend: {
                position: 'right'
            },
            padding: {
                right: 160,
                bottom: 40
            }
        });

        return chart;
    } catch (error) {
        console.error('Chart generation error:', error);
        document.getElementById('category-data').innerHTML = 
            `<div class="alert alert-danger">
                Failed to generate chart: ${error.message}
            </div>`;
        return null;
    }
}

// Start the initialization process after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Check if libraries are already loaded
        if (typeof d3 !== 'undefined' && typeof c3 !== 'undefined') {
            librariesReady = true;
            initializeApp();
        } else {
            // Wait a bit for libraries that might be loading slowly
            setTimeout(checkLibrariesAndInitialize, 500);
        }
    } catch (error) {
        console.error('Error during initialization:', error);
        document.getElementById('category-data').innerHTML = `
            <div class="alert alert-danger">
                ${error.message || 'Failed to initialize chart. Please check console for details.'}
            </div>
        `;
    }
});
</script>