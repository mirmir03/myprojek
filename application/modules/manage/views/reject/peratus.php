<div class="container">
    <h2>Kira Peratusan Reject</h2>
    <a href="<?= module_url('reject/graph') ?>" class="btn btn-secondary mb-3">Kembali</a>
    
    <?php if (!empty($show_table)): ?>
        <!-- Show table with percentages -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-section">
                    <h3>LAPORAN REJECT DENGAN PERATUSAN</h3>
                    <a href="<?= module_url('reject') ?>" class="btn btn-secondary mb-3">Kembali</a>
                    
                    <table class="report-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 35%;">JENIS RALAT</th>
                                <?php if (empty($selected_month)): ?>
                                    <th colspan="12">BULAN</th>
                                <?php else: ?>
                                    <th colspan="1">BULAN TERPILIH</th>
                                <?php endif; ?>
                                <th rowspan="2">JUMLAH</th>
                                <th rowspan="2">PERATUSAN (%)</th>
                            </tr>
                            <tr>
                                <?php if (empty($selected_month)): ?>
                                    <th>Jan</th>
                                    <th>Feb</th>
                                    <th>Mac</th>
                                    <th>Apr</th>
                                    <th>Mei</th>
                                    <th>Jun</th>
                                    <th>Jul</th>
                                    <th>Ogs</th>
                                    <th>Sep</th>
                                    <th>Okt</th>
                                    <th>Nov</th>
                                    <th>Dis</th>
                                <?php else: 
                                    $months = ['', 'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 
                                              'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
                                    $month_name = $months[$selected_month];
                                ?>
                                    <th><?= $month_name ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($table_data)): ?>
                                <?php foreach ($table_data as $index => $row): ?>
                                    <tr>
                                        <td class="error-type"><?= ($index + 1) ?>. <?= $row['REJECT_TYPE'] ?></td>
                                        <?php if (empty($selected_month)): ?>
                                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                                <td style="text-align: center;">
                                                    <?php 
                                                    $count = 0;
                                                    if (isset($row['m'.$m]) && is_numeric($row['m'.$m])) {
                                                        $count = (int)$row['m'.$m];
                                                    }
                                                    echo ($count > 0) ? $count : '-';
                                                    ?>
                                                </td>
                                            <?php endfor; ?>
                                        <?php else: ?>
                                            <td style="text-align: center;">
                                                <?php 
                                                $count = 0;
                                                if (isset($row['m'.$selected_month]) && is_numeric($row['m'.$selected_month])) {
                                                    $count = (int)$row['m'.$selected_month];
                                                }
                                                echo ($count > 0) ? $count : '-';
                                                ?>
                                            </td>
                                        <?php endif; ?>
                                        <td style="text-align: center;"><?= $row['TOTAL'] ?></td>
                                        <td style="text-align: center;">
                                            <?php 
                                            $overall_percentage = ($product_count > 0) ? (array_sum(array_column($table_data, 'TOTAL')) / $product_count) * 100 : 0;
                                            echo number_format($overall_percentage, 2);
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <tr class="total-row">
                                    <td><strong>JUMLAH KESELURUHAN</strong></td>
                                    <?php if (empty($selected_month)): ?>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <?php 
                                            $monthly_total = 0;
                                            foreach ($table_data as $row) {
                                                if (isset($row['m'.$m])) {
                                                    $monthly_total += $row['m'.$m];
                                                }
                                            }
                                            ?>
                                            <td style="text-align: center;"><strong><?= ($monthly_total > 0) ? $monthly_total : '-' ?></strong></td>
                                        <?php endfor; ?>
                                    <?php else: ?>
                                        <?php 
                                        $selected_month_total = 0;
                                        foreach ($table_data as $row) {
                                            if (isset($row['m'.$selected_month])) {
                                                $selected_month_total += $row['m'.$selected_month];
                                            }
                                        }
                                        ?>
                                        <td style="text-align: center;"><strong><?= ($selected_month_total > 0) ? $selected_month_total : '-' ?></strong></td>
                                    <?php endif; ?>
                                    <td style="text-align: center;"><strong><?= array_sum(array_column($table_data, 'TOTAL')) ?></strong></td>
                                    <td style="text-align: center;">
                                        <strong>
                                        <?php 
                                        $overall_percentage = ($product_count > 0) ? (array_sum(array_column($table_data, 'TOTAL')) / $product_count) * 100 : 0;
                                        echo number_format($overall_percentage, 2);
                                        ?>
                                        </strong>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="15" class="no-data">Tiada data untuk paparan ini.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Show calculator form -->
        <div class="card mt-4">
            <div class="card-body">
                <form id="percentageForm" method="post" class="form-horizontal">
                    <input type="hidden" name="show_table" value="1">
                    
                    <div class="form-group row">
                        <label for="year" class="col-sm-3 col-form-label">Tahun:</label>
                        <div class="col-sm-9">
                            <select name="year" id="year" class="form-control">
                                <option value="">Pilih Tahun</option>
                                <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                                    <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Formula:</label>
                        <div class="col-sm-9">
                            <div class="formula-container">
                                <div class="formula-row">
                                    <div class="formula-label">Jumlah Reject</div>
                                    <input type="number" class="form-control formula-input" id="reject_count" value="<?= $total_rejects ?>" readonly>
                                </div>
                                <div class="formula-divider"></div>
                                <div class="formula-row">
                                    <div class="formula-label">Jumlah Produk</div>
                                    <input type="number" class="form-control formula-input" id="product_count" name="product_count" required>
                                </div>
                                <div class="formula-equals">× 100 =</div>
                                <div class="formula-result" id="percentage_result">%</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="button" class="btn btn-primary" onclick="calculatePercentage()">Kira Peratusan</button>
                            <button type="button" class="btn btn-success" onclick="showTable()">Papar Jadual</button>
                        </div>
                    </div>
                </form>
                
                <!-- Table will be displayed here after clicking "Papar Jadual" -->
                <div id="percentageTable" style="display: none; margin-top: 30px;">
                    <div class="table-section">
                        <h3>LAPORAN REJECT DENGAN PERATUSAN</h3>
                        <p>Berdasarkan jumlah produk: <span id="displayProductCount">-</span></p>
                        
                        <table class="report-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 35%;">JENIS RALAT</th>
                                    <th colspan="12">BULAN</th>
                                    <th rowspan="2">JUMLAH</th>
                                    <th rowspan="2">PERATUSAN (%)</th>
                                </tr>
                                <tr>
                                    <th>Jan</th><th>Feb</th><th>Mac</th><th>Apr</th><th>Mei</th><th>Jun</th>
                                    <th>Jul</th><th>Ogs</th><th>Sep</th><th>Okt</th><th>Nov</th><th>Dis</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Table data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.formula-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 300px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.formula-row {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    width: 100%;
}

.formula-label {
    width: 120px;
    text-align: right;
    padding-right: 10px;
    font-weight: bold;
}

.formula-input {
    width: 100px;
    text-align: center;
}

.formula-divider {
    width: 100%;
    height: 1px;
    background-color: #333;
    margin: 10px 0;
}

.formula-equals {
    margin: 10px 0;
    font-size: 18px;
    font-weight: bold;
}

.formula-result {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
    padding: 5px 15px;
    border: 1px solid #007bff;
    border-radius: 5px;
    background-color: white;
}

.card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.table-section h3 {
    margin-bottom: 15px;
    color: #333;
}

.report-table {
    border-collapse: collapse;
    font-size: 12px;
}

.report-table th, .report-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

.report-table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.error-type {
    text-align: left !important;
    padding-left: 10px;
}

.total-row {
    background-color: #e9ecef;
}

.no-data {
    text-align: center;
    font-style: italic;
    color: #666;
}
</style>

<script>
$(document).ready(function() {
    // Handle year change
    $('#year').change(function() {
        const year = $(this).val();
        if (year) {
            window.location.href = '<?= module_url("reject/peratus") ?>?year=' + year;
        }
    });
});

function calculatePercentage() {
    const rejectCount = parseFloat($('#reject_count').val());
    const productCount = parseFloat($('#product_count').val());
    
    if (isNaN(productCount)) {
        alert('Sila masukkan jumlah produk');
        return;
    }
    
    if (productCount <= 0) {
        alert('Jumlah produk mesti lebih besar daripada 0');
        return;
    }
    
    const percentage = (rejectCount / productCount) * 100;
    $('#percentage_result').text(percentage.toFixed(2) + '%');
}

function showTable() {
    const productCount = parseFloat($('#product_count').val());
    
    if (isNaN(productCount) || productCount <= 0) {
        alert('Sila kira peratusan terlebih dahulu');
        return;
    }
    
    // Update display
    $('#displayProductCount').text(productCount);
    
    // Show the table
    $('#percentageTable').show();
    
    // Load table data via AJAX
    const year = $('#year').val() || new Date().getFullYear();
    loadTableData(year, productCount);
}

function loadTableData(year, productCount) {
    const tableBody = $('#tableBody');
    tableBody.html('<tr><td colspan="15" class="no-data">Memuat data...</td></tr>');
    
    $.ajax({
        url: '<?= module_url("reject/get_percentage_table_data") ?>',
        type: 'POST',
        data: {
            year: year,
            product_count: productCount
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let tableHtml = '';
                
                // Generate table rows
                response.table_data.forEach(function(row, index) {
                    tableHtml += '<tr>';
                    tableHtml += '<td class="error-type">' + (index + 1) + '. ' + row.REJECT_TYPE + '</td>';
                    
                    // Monthly columns
                    for (let m = 1; m <= 12; m++) {
                        let count = row['m' + m] || 0;
                        tableHtml += '<td style="text-align: center;">' + (count > 0 ? count : '-') + '</td>';
                    }
                    
                    // Total and percentage columns
                    tableHtml += '<td style="text-align: center;">' + (row.TOTAL || '-') + '</td>';
                    tableHtml += '<td style="text-align: center;">' + response.overall_percentage.toFixed(2) + '</td>';
                    tableHtml += '</tr>';
                });
                
                // Add total row
                tableHtml += '<tr class="total-row">';
                tableHtml += '<td><strong>JUMLAH KESELURUHAN</strong></td>';
                
                // Monthly totals
                for (let m = 1; m <= 12; m++) {
                    let total = response.totals[m] || 0;
                    tableHtml += '<td style="text-align: center;"><strong>' + (total > 0 ? total : '-') + '</strong></td>';
                }
                
                // Grand totals
                tableHtml += '<td style="text-align: center;"><strong>' + response.total_rejects + '</strong></td>';
                tableHtml += '<td style="text-align: center;"><strong>' + response.overall_percentage.toFixed(2) + '</strong></td>';
                tableHtml += '</tr>';
                
                tableBody.html(tableHtml);
            } else {
                tableBody.html('<tr><td colspan="15" class="no-data">Gagal memuat data</td></tr>');
            }
        },
        error: function() {
            tableBody.html('<tr><td colspan="15" class="no-data">Ralat semasa memuat data</td></tr>');
        }
    });
}
</script>