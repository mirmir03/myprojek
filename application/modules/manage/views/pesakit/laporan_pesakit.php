// Flexible chart generator that works with any current dataset
function generateFlexibleChart(data, filterOptions = {}) {
  // Extract unique values for each filter type from current data
  const uniqueBahagianUtama = [...new Set(data.map(item => item.T01_BAHAGIAN_UTAMA))];
  const uniqueKategori = [...new Set(data.map(item => item.T01_KATEGORI))];
  const uniqueGender = [...new Set(data.map(item => item.T01_JANTINA))];
  
  // Default filter values
  const filters = {
    startDate: filterOptions.startDate || null,
    endDate: filterOptions.endDate || null,
    bahagianUtama: filterOptions.bahagianUtama || null,
    kategori: filterOptions.kategori || null
  };
  
  // Apply filters to data
  let filteredData = [...data]; // Create a copy to avoid modifying original data
  
  if (filters.startDate && filters.endDate) {
    filteredData = filteredData.filter(item => {
      // Parse date (assuming DD/MM/YYYY format)
      const parts = item.T01_TARIKH.split('/');
      const itemDate = new Date(parts[2], parts[1] - 1, parts[0]);
      
      const startParts = filters.startDate.split('-');
      const startDate = new Date(startParts[0], startParts[1] - 1, startParts[2]);
      
      const endParts = filters.endDate.split('-');
      const endDate = new Date(endParts[0], endParts[1] - 1, endParts[2]);
      
      return itemDate >= startDate && itemDate <= endDate;
    });
  }
  
  if (filters.bahagianUtama) {
    filteredData = filteredData.filter(item => item.T01_BAHAGIAN_UTAMA === filters.bahagianUtama);
  }
  
  if (filters.kategori) {
    filteredData = filteredData.filter(item => item.T01_KATEGORI === filters.kategori);
  }
  
  // Determine what to use for x-axis based on filtered data
  let xAxisField, chartTitle;
  
  if (filters.bahagianUtama) {
    // If bahagian utama is filtered, group by kategori
    xAxisField = 'T01_KATEGORI';
    chartTitle = `Distribution by Category for ${filters.bahagianUtama}`;
  } else if (filters.kategori) {
    // If kategori is filtered, group by bahagian utama
    xAxisField = 'T01_BAHAGIAN_UTAMA';
    chartTitle = `Distribution by Main Section for ${filters.kategori}`;
  } else {
    // Default grouping by bahagian utama
    xAxisField = 'T01_BAHAGIAN_UTAMA';
    chartTitle = 'Distribution by Main Section';
  }
  
  // Group data by chosen field and gender
  const groupedData = {};
  filteredData.forEach(item => {
    const xValue = item[xAxisField];
    const gender = item.T01_JANTINA;
    
    if (!groupedData[xValue]) {
      // Initialize with 0 count for each possible gender
      groupedData[xValue] = {};
      uniqueGender.forEach(g => {
        groupedData[xValue][g] = 0;
      });
    }
    
    groupedData[xValue][gender]++;
  });
  
  // Transform for C3.js
  const categories = Object.keys(groupedData);
  
  // Create data columns for each gender
  const columns = uniqueGender.map(gender => {
    const column = [gender];
    categories.forEach(category => {
      column.push(groupedData[category][gender] || 0);
    });
    return column;
  });
  
  // Check if we have any data after filtering
  if (categories.length === 0) {
    // Display no data message
    document.getElementById('category-data').innerHTML = '<div class="text-center p-5"><h4>No data available for the selected filters</h4></div>';
    return null;
  }
  
  // Generate chart
  const chart = c3.generate({
    bindto: '#category-data',
    data: {
      columns: columns,
      type: 'bar',
      groups: [uniqueGender]
    },
    title: {
      text: chartTitle
    },
    axis: {
      x: {
        type: 'category',
        categories: categories,
        tick: {
          rotate: categories.length > 5 ? -45 : 0,
          multiline: false,
          culling: {
            max: 15
          }
        },
        height: categories.length > 5 ? 80 : 40
      }
    },
    bar: {
      width: {
        ratio: categories.length > 10 ? 0.3 : 0.6
      }
    },
    color: {
      pattern: ['#7460ee', '#009efb', '#f62d51', '#ffbc34', '#36bea6']
    },
    grid: {
      y: {
        show: true
      }
    },
    tooltip: {
      grouped: true,
      format: {
        value: function(value) {
          return value + ' patients';
        }
      }
    },
    legend: {
      position: 'bottom'
    },
    padding: {
      right: 20
    }
  });
  
  return chart;
}

// Function to create/update filter controls based on current data
function updateFilterControls(data) {
  const uniqueBahagianUtama = [...new Set(data.map(item => item.T01_BAHAGIAN_UTAMA))].sort();
  const uniqueKategori = [...new Set(data.map(item => item.T01_KATEGORI))].sort();
  
  // Create datalist for bahagian utama
  document.getElementById('bahagianUtamaOptions').innerHTML = uniqueBahagianUtama.map(item => 
    `<option value="${item}">${item}</option>`
  ).join('');
  
  // Create datalist for kategori
  document.getElementById('kategoriOptions').innerHTML = uniqueKategori.map(item => 
    `<option value="${item}">${item}</option>`
  ).join('');
}

// Initialize the chart and filters
function initializeChart() {
  // This would be replaced with your AJAX call to get current data
  fetch('/api/patient-data') // Replace with your actual endpoint
    .then(response => response.json())
    .then(data => {
      // Store data in global variable for filtering
      window.currentData = data;
      
      // Update filter controls based on current data
      updateFilterControls(data);
      
      // Generate initial chart
      window.currentChart = generateFlexibleChart(data);
      
      // Setup event listeners for filter controls
      document.getElementById('applyFilters').addEventListener('click', function() {
        const filterOptions = {
          startDate: document.getElementById('startDate').value,
          endDate: document.getElementById('endDate').value,
          bahagianUtama: document.getElementById('bahagianUtama').value,
          kategori: document.getElementById('kategori').value
        };
        
        if (window.currentChart) {
          window.currentChart.destroy();
        }
        window.currentChart = generateFlexibleChart(window.currentData, filterOptions);
      });
      
      document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('bahagianUtama').value = '';
        document.getElementById('kategori').value = '';
        
        if (window.currentChart) {
          window.currentChart.destroy();
        }
        window.currentChart = generateFlexibleChart(window.currentData);
      });
    })
    .catch(error => {
      console.error('Error fetching data:', error);
      document.getElementById('category-data').innerHTML = 
        '<div class="alert alert-danger">Error loading data. Please try again later.</div>';
    });
}

// HTML structure for filter controls
document.addEventListener('DOMContentLoaded', function() {
  const filterContainer = document.createElement('div');
  filterContainer.classList.add('filter-controls', 'mb-3');
  filterContainer.innerHTML = `
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Filter Options</h5>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Start Date</label>
              <input type="date" id="startDate" class="form-control">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>End Date</label>
              <input type="date" id="endDate" class="form-control">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Bahagian Utama</label>
              <input list="bahagianUtamaOptions" id="bahagianUtama" class="form-control" placeholder="Select or type">
              <datalist id="bahagianUtamaOptions"></datalist>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>Kategori</label>
              <input list="kategoriOptions" id="kategori" class="form-control" placeholder="Select or type">
              <datalist id="kategoriOptions"></datalist>
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
  `;
  
  // Insert filter controls before the chart
  document.getElementById('category-data').parentNode.parentNode.insertBefore(
    filterContainer, 
    document.getElementById('category-data').parentNode
  );
  
  // Initialize the chart
  initializeChart();
  
  // Add refresh button
  const refreshButton = document.createElement('button');
  refreshButton.id = 'refreshData';
  refreshButton.className = 'btn btn-outline-info float-right';
  refreshButton.innerHTML = '<i class="fa fa-refresh"></i> Refresh Data';
  document.querySelector('.card-title').appendChild(refreshButton);
  
  // Add event listener for refresh button
  document.getElementById('refreshData').addEventListener('click', function() {
    initializeChart(); // Re-fetch data and rebuild chart
  });
});