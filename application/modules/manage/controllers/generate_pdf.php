<?php
require_once __DIR__ . '/vendor/autoload.php'; // Adjust path to mPDF autoload

function generate_pdf() {
    ob_start();
    ?>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .chart-title {
                text-align: center;
                font-size: 20px;
                margin-bottom: 20px;
            }
            #chart_div {
                height: 500px;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="chart-title">Laporan Statistik Pesakit Mengikut Jantina</div>
        <div id="chart_div">
            <img src="path/to/generated/chart_image.png" alt="Chart Image" style="width: 100%;">
        </div>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output('laporan_pesakit.pdf', 'D'); // Force download
}

// Example usage:
if ($_GET['action'] === 'generate_pdf') {
    generate_pdf();
}
?>
