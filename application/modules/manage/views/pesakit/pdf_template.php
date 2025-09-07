<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Patient Statistics Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #333;
            font-size: 16px;
        }
        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .filter-info strong {
            color: #495057;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        th, td { 
            border: 1px solid #333; 
            padding: 8px; 
            text-align: center;
            vertical-align: middle;
        }
        th { 
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            font-size: 10px;
        }
        .bahagian-header { 
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .grand-total { 
            background-color: #d1ecf1;
            font-weight: bold;
            color: #0c5460;
        }
        .remark-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .remark-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        .remark-content {
            line-height: 1.4;
            color: #6c757d;
            font-style: italic;
        }
        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 9px;
            color: #6c757d;
        }
        .debug-info {
            display: none; /* Hidden in production */
            background: #ffeeee;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ff0000;
        }
    </style>
</head>
<body>
    <!-- Debug information (hidden) -->
    <div class="debug-info">
        <h4>Debug Information:</h4>
        <p>Records count: <?php echo count($records ?? []); ?></p>
        <p>Bahagian Utama: <?php echo isset($filters['bahagian_utama']) ? htmlspecialchars($filters['bahagian_utama']) : 'NOT SET'; ?></p>
        <p>Bulan: <?php echo isset($filters['bulan']) ? htmlspecialchars($filters['bulan']) : 'NOT SET'; ?></p>
        <p>Tahun: <?php echo isset($filters['tahun']) ? htmlspecialchars($filters['tahun']) : 'NOT SET'; ?></p>
        <p>Remark: <?php echo isset($filters['remark']) ? htmlspecialchars($filters['remark']) : 'NOT SET'; ?></p>
        <?php if (!empty($records)): ?>
            <p>First record: <?php print_r($records[0]); ?></p>
        <?php endif; ?>
    </div>

    <div class="header">
        <h2>Patient Statistics Report</h2>
    </div>
    
    <div class="filter-info">
        <strong>Department:</strong> <?php echo isset($filters['bahagian_utama']) && !empty($filters['bahagian_utama']) ? htmlspecialchars($filters['bahagian_utama']) : 'All'; ?> |
        <strong>Month:</strong> <?php echo isset($filters['bulan_name']) && !empty($filters['bulan_name']) ? htmlspecialchars($filters['bulan_name']) : (isset($filters['bulan']) ? 'Month ' . $filters['bulan'] : 'All'); ?> |
        <strong>Year:</strong> <?php echo isset($filters['tahun']) && !empty($filters['tahun']) ? htmlspecialchars($filters['tahun']) : 'All'; ?> |
        <strong>Generated:</strong> <?php echo isset($generated_date) ? $generated_date : date('Y-m-d H:i:s'); ?>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Bahagian Utama</th>
                <th style="width: 35%;">Sub Bahagian</th>
                <th style="width: 10%;">L</th>
                <th style="width: 10%;">P</th>
                <th style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($records)): 
                $grouped = [];
                $grandL = $grandP = $grandTotal = 0;
                
                // Group the data
                foreach ($records as $row) {
                    $bahagian = $row->bahagian_utama ?? $row->T01_BAHAGIAN_UTAMA ?? 'N/A';
                    $sub = $row->sub_bahagian ?? $row->T01_SUB_BAHAGIAN ?? 'N/A';
                    $jantina = $row->jantina ?? $row->T01_JANTINA ?? '';
                    $total = isset($row->total) ? (int)$row->total : (isset($row->TOTAL) ? (int)$row->TOTAL : 1);
                    
                    if (!isset($grouped[$bahagian][$sub])) {
                        $grouped[$bahagian][$sub] = ['L'=>0, 'P'=>0, 'total'=>0];
                    }
                    
                    if (strtolower($jantina) === 'lelaki' || strtolower($jantina) === 'l') {
                        $grouped[$bahagian][$sub]['L'] += $total;
                        $grandL += $total;
                    } else if (strtolower($jantina) === 'perempuan' || strtolower($jantina) === 'p') {
                        $grouped[$bahagian][$sub]['P'] += $total;
                        $grandP += $total;
                    }
                    $grouped[$bahagian][$sub]['total'] += $total;
                    $grandTotal += $total;
                }
                
                // Display grouped data
                foreach ($grouped as $bahagian => $subs): 
                    $bahagian_row_count = count($subs);
                    $first_sub = true;
                    
                    foreach ($subs as $sub => $counts): ?>
                        <tr class="bahagian-header">
                            <?php if ($first_sub): ?>
                                <td rowspan="<?php echo $bahagian_row_count; ?>" style="vertical-align: middle;">
                                    <?php echo htmlspecialchars($bahagian); ?>
                                </td>
                                <?php $first_sub = false; ?>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($sub); ?></td>
                            <td><?php echo $counts['L']; ?></td>
                            <td><?php echo $counts['P']; ?></td>
                            <td><?php echo $counts['total']; ?></td>
                        </tr>
                    <?php endforeach;
                endforeach; ?>
                
                <tr class="grand-total">
                    <td colspan="2"><strong>Grand Total</strong></td>
                    <td><strong><?php echo $grandL; ?></strong></td>
                    <td><strong><?php echo $grandP; ?></strong></td>
                    <td><strong><?php echo $grandTotal; ?></strong></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-data">
                        No records found for the selected criteria:<br>
                        Department: <?php echo isset($filters['bahagian_utama']) ? htmlspecialchars($filters['bahagian_utama']) : 'N/A'; ?><br>
                        Month: <?php echo isset($filters['bulan']) ? htmlspecialchars($filters['bulan']) : 'N/A'; ?><br>
                        Year: <?php echo isset($filters['tahun']) ? htmlspecialchars($filters['tahun']) : 'N/A'; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Remark Section with Better Handling -->
    <div class="remark-section">
        <div class="remark-title">Remarks:</div>
        <div class="remark-content">
            <?php 
            if (isset($filters['remark']) && !empty($filters['remark'])) {
                echo nl2br(htmlspecialchars($filters['remark']));
            } else {
                echo 'No remarks available for this report.';
            }
            ?>
        </div>
    </div>
    
    <div class="footer">
        Report generated on <?php echo isset($generated_date) ? $generated_date : date('Y-m-d H:i:s'); ?>
    </div>
</body>
</html>