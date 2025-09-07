<?php
// Enable all error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class Pesakit extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pesakit_model', 'pesakit_model'); // alias lowercase
    $this->load->model('Remark_model', 'remark_model');
    }

    public function listpesakit()
{
    $data = $this->pesakit_model->get_all_pesakit();
    
    $this->template->title("Senarai pesakit");
    $this->template->set_breadcrumb("Senarai Pesakit");
    $this->template->set("data", $data);
    $this->template->render();
       // $this->load->view('listpesakit');

}

    public function delete($id_pesakit, $id2 = "")
{
    $patient = $this->db->where("T01_ID_PESAKIT", $id_pesakit)
                        ->get("EV_T01_PESAKIT")->row();

    if (!empty($patient->T01_DOCTOR_COMMENT)) {
        $this->session->set_flashdata('error',
            'Cannot delete patient record - doctor comment exists');
        redirect(module_url("pesakit/listpesakit"));
        return;
    }

    if ($this->pesakit_model->soft_delete($id_pesakit)) {
        $this->session->set_flashdata('success',
            'Patient record soft‑deleted successfully');
    } else {
        $this->session->set_flashdata('error',
            'Soft delete failed');
    }

    redirect(module_url("pesakit/listpesakit"));
}



    // Fixed debug_warga_connection method
// Fixed debug method with proper COUNT query
public function debug_warga_connection()
{
    echo "<h3>Database Connection Test</h3>";
    try {
        // Fix the COUNT query
        $query = $this->db->get("EV_T01_WARGA");
        $total = $query->num_rows();
        echo "✓ Database connection OK. Total records in EV_T01_WARGA: " . $total . "<br>";
        
        // Alternative method using count_all_results
        $count = $this->db->count_all('EV_T01_WARGA');
        echo "✓ Alternative count method: " . $count . "<br>";
        
    } catch (Exception $e) {
        echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
        return;
    }
    
    echo "<h3>Sample Data from EV_T01_WARGA</h3>";
    $this->db->select("T01_NO_PENGENALAN, T01_NAMA_WARGA, T01_JANTINA");
    $this->db->limit(5);
    $query = $this->db->get("EV_T01_WARGA");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th style='padding: 5px; border: 1px solid black;'>NO_PENGENALAN</th><th style='padding: 5px; border: 1px solid black;'>NAMA_WARGA</th><th style='padding: 5px; border: 1px solid black;'>JANTINA</th></tr>";
    foreach ($query->result() as $row) {
        echo "<tr>";
        echo "<td style='padding: 5px; border: 1px solid black;'>" . $row->T01_NO_PENGENALAN . "</td>";
        echo "<td style='padding: 5px; border: 1px solid black;'>" . $row->T01_NAMA_WARGA . "</td>";
        echo "<td style='padding: 5px; border: 1px solid black;'>" . $row->T01_JANTINA . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Test Specific Search</h3>";
    $test_ic = "030425-04-0678";
    $this->db->select("T01_NAMA_WARGA as nama, T01_JANTINA");
    $this->db->where("T01_NO_PENGENALAN", $test_ic);
    $query = $this->db->get("EV_T01_WARGA");
    
    echo "Searching for IC: " . $test_ic . "<br>";
    echo "Query: " . $this->db->last_query() . "<br>";
    echo "Rows found: " . $query->num_rows() . "<br>";
    
    if ($query->num_rows() > 0) {
        $result = $query->row();
        echo "Found: " . $result->nama . " (" . $result->T01_JANTINA . ")<br>";
    } else {
        echo "No records found<br>";
    }
}

// Test method to verify AJAX is working
public function test_ajax()
{
    header('Content-Type: application/json');
    
    $no_rujukan = $this->input->post("no_rujukan");
    $kategori = $this->input->post("kategori");
    
    echo json_encode([
        'status' => 'success',
        'message' => 'AJAX is working',
        'received_data' => [
            'no_rujukan' => $no_rujukan,
            'kategori' => $kategori
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    exit;
}

// Fixed get_patient_data method - simplified and working
// Updated get_patient_data method in Pesakit controller
public function get_patient_data()
{
    // Set JSON header
    header('Content-Type: application/json');
    
    try {
        $no_rujukan = $this->input->post("no_rujukan");
        $kategori = $this->input->post("kategori");

        // Validate input
        if (empty($no_rujukan) || empty($kategori)) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Missing required parameters'
            ]);
            exit;
        }

        // Process for staff/student - fetch both name and gender
        if ($kategori == 'pelajar' || $kategori == 'staf') {
            $this->db->select("T01_NAMA_WARGA as nama, T01_JANTINA as jantina");
            $this->db->where("T01_NO_PENGENALAN", $no_rujukan);
            $query = $this->db->get("EV_T01_WARGA");
            
            if ($query->num_rows() > 0) {
    $result = $query->row();

    // 🔍 Debug log
    log_message('debug', 'RAW DB result: ' . print_r($result, true));
    log_message('debug', 'Nama: ' . $result->nama);
    log_message('debug', 'Jantina: ' . (isset($result->jantina) ? $result->jantina : 'Not set'));
                echo json_encode([
                    'status' => 'success', 
                    'data' => [
                        'nama' => $result->nama,
                        'jantina' => $result->jantina  // Use database gender
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => 'not_found', 
                    'message' => 'No rujukan tidak ditemui dalam sistem.'
                ]);
            }
        } else {
            // Handle other categories - derive gender from IC
            if (preg_match('/^\d{6}-\d{2}-\d{4}$/', $no_rujukan)) {
                $lastDigit = substr($no_rujukan, -1);
                $jantina = ((int)$lastDigit % 2 === 0) ? 'Perempuan' : 'Lelaki';
                
                echo json_encode([
                    'status' => 'success', 
                    'data' => [
                        'nama' => '', // Empty for other categories
                        'jantina' => $jantina
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Format No Rujukan tidak sah.'
                ]);
            }
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error occurred'
        ]);
    }
    
    exit;
}

// Simplified test method
public function test_simple_query()
{
    header('Content-Type: application/json');
    
    try {
        $test_ic = "030425-04-0678"; // Use from your sample data
        
        $this->db->select("T01_NAMA_WARGA as nama, T01_JANTINA");
        $this->db->where("T01_NO_PENGENALAN", $test_ic);
        $query = $this->db->get("EV_T01_WARGA");
        
        echo json_encode([
            'status' => 'success',
            'query' => $this->db->last_query(),
            'num_rows' => $query->num_rows(),
            'data' => $query->result(),
            'test_ic' => $test_ic
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}


    public function add()
{
    $kategori = $this->input->post("kategori");
    $no_rujukan = $this->input->post("no_rujukan");

    // Validate IC format (for all categories)
    if (!preg_match('/^\d{6}-\d{2}-\d{4}$/', $no_rujukan)) {
        $this->session->set_flashdata('error', 'Format No Rujukan tidak sah. Contoh: 030408-03-0504');
        redirect(module_url("pesakit/form_add"));
        return;
    }

    // Extract last digit to determine gender
    $lastDigit = substr($no_rujukan, -1);
    $jantina = ((int)$lastDigit % 2 === 0) ? 'Perempuan' : 'Lelaki';

    $nama_pesakit = $this->input->post("nama_pesakit");

    // For student/staff: fetch from EV_T01_WARGA by T01_NO_PENGENALAN (not T01_NO_RUJUKAN)
    if ($kategori == 'pelajar' || $kategori == 'staf') {
        $warga = $this->db
            ->select("T01_NAMA_WARGA as nama, T01_JANTINA")
            ->where("T01_NO_PENGENALAN", $no_rujukan)
            ->get("EV_T01_WARGA")
            ->row();

        if ($warga) {
            $nama_pesakit = $warga->nama;
            $jantina = $warga->T01_JANTINA;
        } else {
            $this->session->set_flashdata('error', 'Rekod tidak dijumpai dalam EV_T01_WARGA.');
            redirect(module_url("pesakit/form_add"));
            return;
        }
    }

    if (empty($nama_pesakit)) {
        $this->session->set_flashdata('error', 'Nama Pesakit diperlukan.');
        redirect(module_url("pesakit/form_add"));
        return;
    }

    // Build insert
    $this->db->set("T01_NAMA_PESAKIT", $nama_pesakit);
    $this->db->set("T01_NO_RUJUKAN", $no_rujukan);
    $this->db->set("T01_JANTINA", $jantina);
    $this->db->set("T01_KATEGORI", $kategori);
    $this->db->set("T01_BAHAGIAN_UTAMA", $this->input->post("bhg_utama"));
    $this->db->set("T01_SUB_BAHAGIAN", $this->input->post("sub_bhg"));
    $this->db->set("T01_STATUS", 1);


    $tarikh_input = $this->input->post("tarikh");
    if (!empty($tarikh_input)) {
        $this->db->set("T01_TARIKH", "TO_DATE('$tarikh_input', 'YYYY-MM-DD')", false);
    }

    $this->db->set("T01_ID_PESAKIT", "EV_T01_PESAKIT_SEQ.NEXTVAL", false); // ✅ use sequence for ID
$this->db->insert("EV_T01_PESAKIT");


    $this->session->set_flashdata('success', 'Rekod pesakit berjaya ditambah');
    redirect(module_url("pesakit/listpesakit"));
}


   public function form_add() {
    $this->template->title("Tambah pesakit");
    $this->template->set_breadcrumb("Senarai Pesakit", "pesakit/listpesakit");
    $this->template->set_breadcrumb("Tambah Pesakit");
    $this->template->render();
}


    public function form_edit($id_pesakit)
{
    $pesakit = $this->db
        ->where("T01_ID_PESAKIT", $id_pesakit)
        ->get("EV_T01_PESAKIT")
        ->row();

    // Check if patient has doctor comment
    if (!empty($pesakit->T01_DOCTOR_COMMENT)) {
        $this->session->set_flashdata('error', 'Cannot edit patient record - doctor comment exists');
        redirect(module_url("pesakit/listpesakit"));
    }

    $this->template->set("pesakit", $pesakit);
    $this->template->render();
}

public function save($id_pesakit)
{
    // First check if patient has doctor comment
    $patient = $this->db
        ->where("T01_ID_PESAKIT", $id_pesakit)
        ->get("EV_T01_PESAKIT")
        ->row();

    if (!empty($patient->T01_DOCTOR_COMMENT)) {
        $this->session->set_flashdata('error', 'Cannot update patient record - doctor comment exists');
        redirect(module_url("pesakit/listpesakit"));
    }

    $nama_pesakit = $this->input->post("nama_pesakit");
    $no_rujukan = $this->input->post("no_rujukan");
    $jantina = $this->input->post("jantina");
    $kategori = $this->input->post("kategori");
    $bhg_utama = $this->input->post("bhg_utama");
    $sub_bhg = $this->input->post("sub_bhg");

    $data_to_update = [
        "T01_NAMA_PESAKIT" => $nama_pesakit,
        "T01_NO_RUJUKAN" => $no_rujukan,
        "T01_JANTINA" => $jantina,
        "T01_KATEGORI" => $kategori,
        "T01_BAHAGIAN_UTAMA" => $bhg_utama,
        "T01_SUB_BAHAGIAN" => $sub_bhg,
    ];

    $this->db
        ->where("T01_ID_PESAKIT", $id_pesakit)
        ->update("EV_T01_PESAKIT", $data_to_update);

    $this->session->set_flashdata('success', 'Patient record updated successfully');
    redirect(module_url("pesakit/listpesakit"));
}

// Add at class level
protected $available_years_cache = null;

public function get_available_years()
{
    if ($this->available_years_cache !== null) {
        echo json_encode([
            'status' => 'success',
            'years' => $this->available_years_cache
        ]);
        exit;
    }

    header('Content-Type: application/json');
    
    try {
        // Query to get distinct years from patient records
        $sql = "SELECT DISTINCT EXTRACT(YEAR FROM T01_TARIKH) as year 
                FROM EV_T01_PESAKIT 
                WHERE T01_STATUS = 1
                ORDER BY year DESC";
        
        $stmt = oci_parse($this->db->conn_id, $sql);
        oci_execute($stmt);
        
        $years = array();
        while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
            $years[] = (int)$row['YEAR'];
        }
        
        oci_free_statement($stmt);
        
        // Set cache before returning
        $this->available_years_cache = $years;
        
        echo json_encode([
            'status' => 'success',
            'years' => $years
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}

private function validate_year($year)
{
    if (!empty($year)) {
        $valid_year = $this->db->query("
            SELECT 1 FROM EV_T01_PESAKIT 
            WHERE EXTRACT(YEAR FROM T01_TARIKH) = ? 
            AND T01_STATUS = 1
            AND ROWNUM = 1
        ", [$year])->row();

        if (!$valid_year) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Selected year has no patient records'
            ]);
            exit;
        }
    }
}

   // FIXED GRAPH METHODS
    public function patient_graph()
{
    // Get available years from database
    $sql = "SELECT DISTINCT EXTRACT(YEAR FROM T01_TARIKH) as year 
            FROM EV_T01_PESAKIT 
            WHERE T01_STATUS = 1
            ORDER BY year DESC";
    
    $stmt = oci_parse($this->db->conn_id, $sql);
    oci_execute($stmt);
    
    $years = array();
    while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
        $years[] = (int)$row['YEAR'];
    }
    
    oci_free_statement($stmt);
    
    $this->template->set("available_years", $years);
    $this->template->title("Patient Statistics Graph");
    $this->template->render();
}

    // UPDATED: Replace your existing get_graph_data method with this
// Add this new method to your Pesakit controller

public function get_graph_data()
{
    // Set proper headers for JSON response
    header('Content-Type: application/json');
    
    // Get POST data
    $bahagian_utama = $this->input->post('bahagian_utama');
    $month = $this->input->post('month');
    $year = $this->input->post('year');

    // Validate required fields - now bahagian_utama can be empty for "All"
    if ($bahagian_utama === null) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Bahagian Utama parameter is required'
        ]);
        exit;
    }

    try {
        // Build SQL query - Only active records (T01_STATUS = 1)
        if (empty($bahagian_utama)) {
            // ALL BAHAGIAN UTAMA - Stacked chart by bahagian utama
            $sql = "SELECT 
                        EXTRACT(MONTH FROM p.T01_TARIKH) as month,
                        p.T01_BAHAGIAN_UTAMA as bahagian_utama,
                        p.T01_JANTINA as jantina,
                        COUNT(*) as total
                    FROM EV_T01_PESAKIT p
                    WHERE p.T01_STATUS = 1";

            $bind_params = array();

            // Add month filter if selected
            if (!empty($month)) {
                $sql .= " AND EXTRACT(MONTH FROM p.T01_TARIKH) = :month";
                $bind_params[':month'] = $month;
            }

            // Add year filter if selected
            if (!empty($year)) {
                $valid_year = $this->db->query("
                    SELECT 1 FROM EV_T01_PESAKIT 
                    WHERE EXTRACT(YEAR FROM T01_TARIKH) = ? 
                    AND T01_STATUS = 1
                    AND ROWNUM = 1
                ", [$year])->row();

                if (!$valid_year) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Selected year has no patient records'
                    ]);
                    exit;
                }

                $sql .= " AND EXTRACT(YEAR FROM p.T01_TARIKH) = :year";
                $bind_params[':year'] = $year;
            }

            // Group by month, bahagian utama and gender
            $sql .= " GROUP BY EXTRACT(MONTH FROM p.T01_TARIKH), p.T01_BAHAGIAN_UTAMA, p.T01_JANTINA
                      ORDER BY EXTRACT(MONTH FROM p.T01_TARIKH), p.T01_BAHAGIAN_UTAMA, p.T01_JANTINA";

            // Execute query
            $stmt = oci_parse($this->db->conn_id, $sql);
            
            // Bind parameters
            foreach ($bind_params as $param => $value) {
                oci_bind_by_name($stmt, $param, $bind_params[$param]);
            }
            
            $result = oci_execute($stmt);

            if (!$result) {
                $error = oci_error($stmt);
                throw new Exception('Database query failed: ' . $error['message']);
            }

            // Process results for stacked chart
            $raw_data = array();
            while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
                $raw_data[] = array(
                    'month' => (int)$row['MONTH'],
                    'bahagian_utama' => $row['BAHAGIAN_UTAMA'],
                    'jantina' => $row['JANTINA'],
                    'total' => (int)$row['TOTAL']
                );
            }

            oci_free_statement($stmt);

            // Transform data for stacked chart
            $chart_data = $this->transform_stacked_data_for_chart($raw_data, $month);

        } else {
            // SPECIFIC BAHAGIAN UTAMA - Regular chart by gender
            $sql = "SELECT 
                        EXTRACT(MONTH FROM p.T01_TARIKH) as month,
                        p.T01_JANTINA as jantina,
                        COUNT(*) as total
                    FROM EV_T01_PESAKIT p
                    WHERE p.T01_STATUS = 1 
                    AND p.T01_BAHAGIAN_UTAMA = :bahagian_utama";

            $bind_params = array();
            $bind_params[':bahagian_utama'] = $bahagian_utama;

            // Add month filter if selected
            if (!empty($month)) {
                $sql .= " AND EXTRACT(MONTH FROM p.T01_TARIKH) = :month";
                $bind_params[':month'] = $month;
            }

            // Add year filter if selected
            if (!empty($year)) {
                $valid_year = $this->db->query("
                    SELECT 1 FROM EV_T01_PESAKIT 
                    WHERE EXTRACT(YEAR FROM T01_TARIKH) = ? 
                    AND T01_STATUS = 1
                    AND ROWNUM = 1
                ", [$year])->row();

                if (!$valid_year) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Selected year has no patient records'
                    ]);
                    exit;
                }

                $sql .= " AND EXTRACT(YEAR FROM p.T01_TARIKH) = :year";
                $bind_params[':year'] = $year;
            }

            // Group by month and gender
            $sql .= " GROUP BY EXTRACT(MONTH FROM p.T01_TARIKH), p.T01_JANTINA
                      ORDER BY EXTRACT(MONTH FROM p.T01_TARIKH), p.T01_JANTINA";

            // Execute query
            $stmt = oci_parse($this->db->conn_id, $sql);
            
            // Bind parameters
            foreach ($bind_params as $param => $value) {
                oci_bind_by_name($stmt, $param, $bind_params[$param]);
            }
            
            $result = oci_execute($stmt);

            if (!$result) {
                $error = oci_error($stmt);
                throw new Exception('Database query failed: ' . $error['message']);
            }

            // Process results for regular gender chart
            $raw_data = array();
            while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
                $raw_data[] = array(
                    'month' => (int)$row['MONTH'],
                    'jantina' => $row['JANTINA'],
                    'total' => (int)$row['TOTAL']
                );
            }

            oci_free_statement($stmt);

            // Transform data for regular monthly chart
            $chart_data = $this->transform_monthly_data_for_chart($raw_data, $month);
        }

        // Return success response
        echo json_encode([
            'status' => 'success',
            'data' => $chart_data,
            'chart_type' => empty($bahagian_utama) ? 'stacked' : 'grouped',
            'filters' => [
                'bahagian_utama' => $bahagian_utama,
                'month' => $month,
                'year' => $year
            ],
            'active_records_found' => count($raw_data),
            'note' => 'Only active records (T01_STATUS = 1) are included'
        ]);

    } catch (Exception $e) {
        // Log error
        log_message('error', 'Graph data error: ' . $e->getMessage());
        
        // Return error response
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error occurred: ' . $e->getMessage()
        ]);
    }
    
    exit;
}

// New method for stacked chart data transformation
// Updated transform_stacked_data_for_chart method in Pesakit controller
private function transform_stacked_data_for_chart($raw_data, $selected_month = null)
{
    // Month names for labels
    $month_names = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];

    // Get unique bahagian utama from data
    $bahagian_list = array_unique(array_column($raw_data, 'bahagian_utama'));
    sort($bahagian_list);

    // Color palette for different bahagian utama
    $colors = [
        'rgba(255, 99, 132, 0.8)',   // Red - Abdomen
        'rgba(54, 162, 235, 0.8)',   // Blue - Chest  
        'rgba(255, 205, 86, 0.8)',   // Yellow - Skull and Head
        'rgba(75, 192, 192, 0.8)',   // Green - Spine
        'rgba(153, 102, 255, 0.8)',  // Purple - Upper Extremities
        'rgba(255, 159, 64, 0.8)',   // Orange - Lower Extremities
        'rgba(199, 199, 199, 0.8)',  // Grey
        'rgba(83, 102, 255, 0.8)'    // Indigo
    ];

    $border_colors = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 205, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(199, 199, 199, 1)',
        'rgba(83, 102, 255, 1)'
    ];

    // Create labels: L and P for each month (like grouped chart)
    $labels = [];
    for ($month = 1; $month <= 12; $month++) {
        $labels[] = 'L'; // Lelaki for this month
        $labels[] = 'P'; // Perempuan for this month
    }

    // Initialize datasets for each bahagian utama
    $datasets = [];
    foreach ($bahagian_list as $index => $bahagian) {
        $datasets[] = [
            'label' => $bahagian,
            'data' => array_fill(0, 24, 0), // 12 months × 2 genders = 24 bars
            'backgroundColor' => $colors[$index % count($colors)],
            'borderColor' => $border_colors[$index % count($colors)],
            'borderWidth' => 1
        ];
    }

    // Fill data
    foreach ($raw_data as $row) {
        $month = $row['month'];
        $bahagian = $row['bahagian_utama'];
        $jantina = $row['jantina'];
        $total = $row['total'];

        // Find dataset index for this bahagian
        $dataset_index = array_search($bahagian, $bahagian_list);
        
        if ($dataset_index !== false) {
            // Calculate label index: (month-1) * 2 + gender_offset
            $gender_offset = (strtolower($jantina) === 'lelaki') ? 0 : 1;
            $label_index = ($month - 1) * 2 + $gender_offset;
            
            if ($label_index >= 0 && $label_index < 24) {
                $datasets[$dataset_index]['data'][$label_index] = $total;
            }
        }
    }

    // Add month information for custom rendering
    $month_positions = [];
    for ($month = 1; $month <= 12; $month++) {
        $month_positions[] = [
            'month' => $month_names[$month],
            'start_index' => ($month - 1) * 2,
            'end_index' => ($month - 1) * 2 + 1
        ];
    }

    return [
        'labels' => $labels,
        'datasets' => $datasets,
        'month_positions' => $month_positions // Add this for custom label rendering
    ];
}
// Add this new helper method to your Pesakit controller
private function get_month_positions_for_chart($month_names)
{
    $month_positions = [];
    for ($month = 1; $month <= 12; $month++) {
        $month_positions[] = [
            'month' => $month_names[$month],
            'start_index' => ($month - 1) * 2,
            'end_index' => ($month - 1) * 2 + 1
        ];
    }
    return $month_positions;
}
// Replace your existing transform_monthly_data_for_chart method with this updated version
private function transform_monthly_data_for_chart($raw_data, $selected_month = null)
{
    // Month names for labels
    $month_names = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
    ];

    // Create labels: L and P for each month (like stacked chart)
    $labels = [];
    for ($month = 1; $month <= 12; $month++) {
        $labels[] = 'L'; // Lelaki for this month
        $labels[] = 'P'; // Perempuan for this month
    }

    // Initialize data arrays for 24 positions (12 months × 2 genders)
    $lelaki_data = array_fill(0, 24, 0);
    $perempuan_data = array_fill(0, 24, 0);
    
    // Fill data for each month that has records
    foreach ($raw_data as $row) {
        $month = $row['month'];
        $jantina = strtolower($row['jantina']);
        $total = $row['total'];
        
        // Calculate positions: (month-1) * 2 + gender_offset
        $lelaki_index = ($month - 1) * 2 + 0; // L position
        $perempuan_index = ($month - 1) * 2 + 1; // P position
        
        if ($jantina === 'lelaki') {
            $lelaki_data[$lelaki_index] = $total;
        } else if ($jantina === 'perempuan') {
            $perempuan_data[$perempuan_index] = $total;
        }
    }
    
    // If a specific month is selected, highlight it
    if (!empty($selected_month)) {
        $selected_month_index = $selected_month - 1;
        
        // Create background colors array - highlight selected month
        $lelaki_bg = array_fill(0, 24, 'rgba(54, 162, 235, 0.2)');
        $perempuan_bg = array_fill(0, 24, 'rgba(255, 99, 132, 0.2)');
        
        // Highlight selected month bars
        $lelaki_bg[$selected_month_index * 2] = 'rgba(54, 162, 235, 0.8)';
        $perempuan_bg[$selected_month_index * 2 + 1] = 'rgba(255, 99, 132, 0.8)';
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Lelaki',
                    'data' => $lelaki_data,
                    'backgroundColor' => $lelaki_bg,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Perempuan',
                    'data' => $perempuan_data,
                    'backgroundColor' => $perempuan_bg,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ],
            'month_positions' => $this->get_month_positions_for_chart($month_names) // Add month positions
        ];
    }
    
    // Default case - show all months normally
    return [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Lelaki',
                'data' => $lelaki_data,
                'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            ],
            [
                'label' => 'Perempuan',
                'data' => $perempuan_data,
                'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 1
            ]
        ],
        'month_positions' => $this->get_month_positions_for_chart($month_names) // Add month positions
    ];
}
   
    // Debug method to test data retrieval
    public function debug_graph_data()
    {
        // Set proper headers for JSON response
        header('Content-Type: application/json');
        
        $bahagian_utama = $this->input->post('bahagian_utama') ?: $this->input->get('bahagian_utama');
        $kategori = $this->input->post('kategori') ?: $this->input->get('kategori');
        
        if (empty($bahagian_utama) || empty($kategori)) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Bahagian Utama and Kategori parameters are required'
            ]);
            exit;
        }
        
        try {
            $debug_data = $this->pesakit_model->debug_graph_data($bahagian_utama, $kategori);
            
            echo json_encode([
                'status' => 'success', 
                'data' => $debug_data, 
                'query' => $this->db->last_query(),
                'parameters' => [
                    'bahagian_utama' => $bahagian_utama,
                    'kategori' => $kategori
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Debug error: ' . $e->getMessage()
            ]);
        }     
        // Important: Exit to prevent any additional output
        exit;
    }
    public function check_dates() 
    {
        header('Content-Type: application/json');
        $dates = $this->pesakit_model->get_sample_dates(10);
        echo json_encode(['dates' => $dates]);
        exit;
    }    
    public function get_table_data()
{
    header('Content-Type: application/json');
    
    $bahagian_utama = $this->input->post('bahagian_utama');
    $month = $this->input->post('month');
    $year = $this->input->post('year');

    try {
        // Build the SQL query - Only active records
        $sql = "SELECT 
                    p.T01_BAHAGIAN_UTAMA as bahagian_utama,
                    p.T01_SUB_BAHAGIAN as sub_bahagian,
                    p.T01_JANTINA as jantina,
                    COUNT(*) as total
                FROM EV_T01_PESAKIT p
                WHERE p.T01_STATUS = 1";

        $bind_params = array();

        // Add bahagian utama filter if selected and not "All"
        if (!empty($bahagian_utama)) {
            $sql .= " AND p.T01_BAHAGIAN_UTAMA = :bahagian_utama";
            $bind_params[':bahagian_utama'] = $bahagian_utama;
        }

        // Add month filter if selected
        if (!empty($month)) {
            $sql .= " AND EXTRACT(MONTH FROM p.T01_TARIKH) = :month";
            $bind_params[':month'] = $month;
        }

        // Add year filter if selected
        if (!empty($year)) {
            $valid_year = $this->db->query("
                SELECT 1 FROM EV_T01_PESAKIT 
                WHERE EXTRACT(YEAR FROM T01_TARIKH) = ? 
                AND T01_STATUS = 1
                AND ROWNUM = 1
            ", [$year])->row();

            if (!$valid_year) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Selected year has no patient records'
                ]);
                exit;
            }
        

            $sql .= " AND EXTRACT(YEAR FROM p.T01_TARIKH) = :year";
            $bind_params[':year'] = $year;
        }


        // Group by and order by
        $sql .= " GROUP BY p.T01_BAHAGIAN_UTAMA, p.T01_SUB_BAHAGIAN, p.T01_JANTINA
                  ORDER BY p.T01_BAHAGIAN_UTAMA, p.T01_SUB_BAHAGIAN, p.T01_JANTINA";

        // Execute query
        $stmt = oci_parse($this->db->conn_id, $sql);
        
        // Bind parameters
        foreach ($bind_params as $param => $value) {
            oci_bind_by_name($stmt, $param, $bind_params[$param]);
        }
        
        $result = oci_execute($stmt);

        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception('Database query failed: ' . $error['message']);
        }

        $data = array();
        while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
            $data[] = array(
                'bahagian_utama' => $row['BAHAGIAN_UTAMA'],
                'sub_bahagian' => $row['SUB_BAHAGIAN'],
                'jantina' => $row['JANTINA'],
                'total' => (int)$row['TOTAL']
            );
        }

        oci_free_statement($stmt);

        echo json_encode([
            'status' => 'success',
            'data' => $data,
            'filters' => [
                'bahagian_utama' => $bahagian_utama,
                'month' => $month,
                'year' => $year
            ],
            'active_records_found' => count($data),
            'note' => 'Only active records (T01_STATUS = 1) are included'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}
// Optional: Add debug function for table data
public function debug_table_data()
{
    header('Content-Type: application/json');
    
    $bahagian_utama = $this->input->post('bahagian_utama');
    $month = $this->input->post('month');
    $year = $this->input->post('year');

    try {
        // Debug query to see all data - FIXED table name
        $debug_sql = "SELECT 
                        p.T01_BAHAGIAN_UTAMA,
                        p.T01_SUB_BAHAGIAN,
                        p.T01_TARIKH,
                        EXTRACT(MONTH FROM p.T01_TARIKH) as month_num,
                        EXTRACT(YEAR FROM p.T01_TARIKH) as year_num
                      FROM EV_T01_PESAKIT p
                      WHERE p.T01_STATUS = 1
                      ORDER BY p.T01_BAHAGIAN_UTAMA, p.T01_SUB_BAHAGIAN";

        $stmt = oci_parse($this->db->conn_id, $debug_sql);
        oci_execute($stmt);

        $debug_data = array();
        while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
            $debug_data[] = $row;
        }

        oci_free_statement($stmt);

        echo json_encode([
            'status' => 'debug',
            'all_data_count' => count($debug_data),
            'sample_data' => array_slice($debug_data, 0, 10), // First 10 records
            'filters_applied' => [
                'bahagian_utama' => $bahagian_utama,
                'month' => $month,
                'year' => $year
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}
public function test_table_structure()
{
    header('Content-Type: application/json');
    
    try {
        // Test if table exists and get structure
        $sql = "SELECT COLUMN_NAME, DATA_TYPE 
                FROM USER_TAB_COLUMNS 
                WHERE TABLE_NAME = 'EV_T01_PESAKIT'
                ORDER BY COLUMN_ID";
        
        $stmt = oci_parse($this->db->conn_id, $sql);
        oci_execute($stmt);

        $columns = array();
        while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
            $columns[] = $row;
        }

        // Also get a sample record
        $sample_sql = "SELECT * FROM EV_T01_PESAKIT WHERE ROWNUM <= 1";
        $sample_stmt = oci_parse($this->db->conn_id, $sample_sql);
        oci_execute($sample_stmt);
        $sample_data = oci_fetch_array($sample_stmt, OCI_ASSOC);

        oci_free_statement($stmt);
        oci_free_statement($sample_stmt);

        echo json_encode([
            'status' => 'success',
            'table_exists' => true,
            'columns' => $columns,
            'sample_data' => $sample_data
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}
// Add to your Pesakit controller

/**
 * Save remark for patient statistics
 */

// Replace your save_remark method with this:
// Update your export_pdf method with correct redirect path
public function export_pdf() {
    try {
        // Accept data from both POST and GET methods
        $bahagian_utama = $this->input->post('bahagian_utama') ?: $this->input->get('bahagian_utama');
        $bulan = $this->input->post('bulan') ?: $this->input->get('bulan');
        $tahun = $this->input->post('tahun') ?: $this->input->get('tahun');

        // Debug: Log the received parameters and method
        log_message('debug', 'PDF Export - Method: ' . $_SERVER['REQUEST_METHOD']);
        log_message('debug', 'PDF Export - Bahagian: ' . $bahagian_utama . ', Bulan: ' . $bulan . ', Tahun: ' . $tahun);
        log_message('debug', 'PDF Export - GET data: ' . print_r($_GET, true));
        log_message('debug', 'PDF Export - POST data: ' . print_r($_POST, true));

        // Validate required parameters
        if (empty($bahagian_utama) || empty($bulan) || empty($tahun)) {
            throw new Exception('Missing required parameters. Received: ' . 
                'bahagian_utama=' . var_export($bahagian_utama, true) . 
                ', bulan=' . var_export($bulan, true) . 
                ', tahun=' . var_export($tahun, true));
        }

        $this->load->model('Pesakit_model');
        $records = $this->Pesakit_model->get_filtered_report($bahagian_utama, $bulan, $tahun);

        // Debug: Log the records count
        log_message('debug', 'PDF Export - Records count: ' . count($records));
        
        // Get remark for this filter combination
        $this->load->model('remark_model');
        $remark = $this->remark_model->get_remark($bahagian_utama, $bulan, $tahun);

        // Prepare data for PDF with proper filter values
        $data = array(
            'records' => $records,
            'filters' => array(
                'bahagian_utama' => $bahagian_utama,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'bulan_name' => $this->get_month_name($bulan),
                'remark' => $remark ? $remark : '' // Ensure remark is not null
            ),
            'generated_date' => date('Y-m-d H:i:s')
        );

        // Debug: Check if records exist
        if (empty($records)) {
            log_message('warning', 'PDF Export - No records found for the given filters');
        } else {
            log_message('debug', 'PDF Export - First record: ' . print_r($records[0], true));
        }

        // Generate HTML
        $html = $this->load->view('pesakit/pdf_template', $data, true);
        
        // Debug: Log HTML content length and check if filters are in HTML
        log_message('debug', 'PDF Export - HTML length: ' . strlen($html));
        log_message('debug', 'PDF Export - Filters in HTML: ' . (strpos($html, $bahagian_utama) !== false ? 'YES' : 'NO'));

        // Load M_pdf library
        $this->load->library('M_pdf');
        
        // Write HTML to PDF
        $this->m_pdf->pdf->WriteHTML($html);
        
        // Generate filename with filters
        $filename = "laporan_pesakit_{$bahagian_utama}_{$bulan}_{$tahun}_" . date('Y-m-d_H-i-s') . ".pdf";
        
        // Output PDF
        $this->m_pdf->pdf->Output($filename, "D");
        
    } catch (Exception $e) {
        log_message('error', 'PDF Export Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        
        // Show error message
        echo "<script>alert('Unable to generate PDF: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
public function debug_pdf_data() {
    header('Content-Type: application/json');
    
    try {
        $bahagian = $this->input->post('bahagian_utama') ?: 'Abdomen';
        $bulan = $this->input->post('bulan') ?: '8';
        $tahun = $this->input->post('tahun') ?: '2025';

        $this->load->model('Pesakit_model');
        $records = $this->Pesakit_model->get_filtered_report($bahagian, $bulan, $tahun);

        // Get remark
        $this->load->model('remark_model');
        $remark = $this->remark_model->get_remark($bahagian, $bulan, $tahun);

        echo json_encode([
            'status' => 'success',
            'filters' => [
                'bahagian_utama' => $bahagian,
                'bulan' => $bulan,
                'tahun' => $tahun
            ],
            'records_count' => count($records),
            'records' => $records,
            'remark' => $remark,
            'sample_record' => !empty($records) ? $records[0] : null
        ], JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    exit;
}

// Helper method to get month name
private function get_month_name($month) {
    $months = array(
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    );
    
    return isset($months[$month]) ? $months[$month] : 'Unknown';
}

// Add this enhanced debug method to your Pesakit controller
public function debug_pdf_filters() {
    echo "<h3>Form Submission Debug</h3>";
    
    // Check request method
    echo "<strong>Request Method:</strong> " . $_SERVER['REQUEST_METHOD'] . "<br><br>";
    
    // Check all POST data
    echo "<strong>All POST Data:</strong><br>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Check all GET data
    echo "<strong>All GET Data:</strong><br>";
    echo "<pre>" . print_r($_GET, true) . "</pre>";
    
    // Check raw input
    echo "<strong>Raw PHP Input:</strong><br>";
    echo "<pre>" . file_get_contents('php://input') . "</pre>";
    
    // Check CodeIgniter input
    echo "<strong>CodeIgniter Input (POST):</strong><br>";
    $bahagian_utama = $this->input->post('bahagian_utama');
    $bulan = $this->input->post('bulan');
    $tahun = $this->input->post('tahun');
    
    echo "Bahagian: " . var_export($bahagian_utama, true) . "<br>";
    echo "Bulan: " . var_export($bulan, true) . "<br>";  
    echo "Tahun: " . var_export($tahun, true) . "<br><br>";
    
    // Check CodeIgniter input (GET)
    echo "<strong>CodeIgniter Input (GET):</strong><br>";
    $bahagian_utama_get = $this->input->get('bahagian_utama');
    $bulan_get = $this->input->get('bulan');
    $tahun_get = $this->input->get('tahun');
    
    echo "Bahagian: " . var_export($bahagian_utama_get, true) . "<br>";
    echo "Bulan: " . var_export($bulan_get, true) . "<br>";  
    echo "Tahun: " . var_export($tahun_get, true) . "<br><br>";
    
    // Test remark retrieval with dummy data
    $this->load->model('remark_model');
    echo "<strong>Testing Remark Model:</strong><br>";
    
    // Try with dummy data first
    $test_remark = $this->remark_model->get_remark('Abdomen', 8, 2025);
    echo "Test Remark (Abdomen, 8, 2025): " . var_export($test_remark, true) . "<br>";
    
    // Check CSRF token
    echo "<strong>CSRF Token:</strong> " . $this->security->get_csrf_token_name() . " = " . $this->security->get_csrf_hash() . "<br>";
}

// Alternative method that works with both GET and POST
public function export_pdf_fixed() {
    try {
        // Try to get data from both POST and GET
        $bahagian_utama = $this->input->post('bahagian_utama') ?: $this->input->get('bahagian_utama');
        $bulan = $this->input->post('bulan') ?: $this->input->get('bulan');
        $tahun = $this->input->post('tahun') ?: $this->input->get('tahun');
        
        // If still empty, try from URI segments (if using URL routing)
        if (empty($bahagian_utama)) {
            $bahagian_utama = $this->uri->segment(3); // Adjust segment number as needed
        }
        if (empty($bulan)) {
            $bulan = $this->uri->segment(4); // Adjust segment number as needed
        }
        if (empty($tahun)) {
            $tahun = $this->uri->segment(5); // Adjust segment number as needed
        }

        // Debug: Log the received parameters
        log_message('debug', 'PDF Export - Method: ' . $_SERVER['REQUEST_METHOD']);
        log_message('debug', 'PDF Export - POST data: ' . print_r($_POST, true));
        log_message('debug', 'PDF Export - GET data: ' . print_r($_GET, true));
        log_message('debug', 'PDF Export - Final values - Bahagian: ' . $bahagian_utama . ', Bulan: ' . $bulan . ', Tahun: ' . $tahun);

        // Validate required parameters
        if (empty($bahagian_utama) || empty($bulan) || empty($tahun)) {
            throw new Exception('Missing required parameters. Bahagian: ' . var_export($bahagian_utama, true) . ', Bulan: ' . var_export($bulan, true) . ', Tahun: ' . var_export($tahun, true));
        }

        $this->load->model('Pesakit_model');
        $records = $this->Pesakit_model->get_filtered_report($bahagian_utama, $bulan, $tahun);

        // Debug: Log the records count
        log_message('debug', 'PDF Export - Records count: ' . count($records));
        
        // Get remark for this filter combination
        $this->load->model('remark_model');
        $remark = $this->remark_model->get_remark($bahagian_utama, $bulan, $tahun);
        log_message('debug', 'PDF Export - Remark: ' . var_export($remark, true));

        // Prepare data for PDF with proper filter values
        $data = array(
            'records' => $records,
            'filters' => array(
                'bahagian_utama' => $bahagian_utama,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'bulan_name' => $this->get_month_name($bulan),
                'remark' => $remark ? $remark : '' // Ensure remark is not null
            ),
            'generated_date' => date('Y-m-d H:i:s')
        );

        // Generate HTML
        $html = $this->load->view('pesakit/pdf_template', $data, true);
        
        // Debug: Log HTML content length and check if filters are in HTML
        log_message('debug', 'PDF Export - HTML length: ' . strlen($html));
        log_message('debug', 'PDF Export - Filters in HTML: ' . (strpos($html, $bahagian_utama) !== false ? 'YES' : 'NO'));

        // Load M_pdf library
        $this->load->library('M_pdf');
        
        // Write HTML to PDF
        $this->m_pdf->pdf->WriteHTML($html);
        
        // Generate filename with filters
        $filename = "laporan_pesakit_{$bahagian_utama}_{$bulan}_{$tahun}_" . date('Y-m-d_H-i-s') . ".pdf";
        
        // Output PDF
        $this->m_pdf->pdf->Output($filename, "D");
        
    } catch (Exception $e) {
        log_message('error', 'PDF Export Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
        
        // Show error message
        echo "<script>alert('Unable to generate PDF: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
public function save_remark()
{
    // Clear ALL output buffers first
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Start fresh output buffer
    ob_start();
    
    try {
        // Get POST data
        $bahagian_utama = $this->input->post('bahagian_utama');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $remark = $this->input->post('remark');
        
        // Basic validation
        if (empty($bahagian_utama) || empty($bulan) || empty($tahun)) {
            $response = [
                'status' => 'error',
                'message' => 'Bahagian Utama, Bulan, and Tahun are required'
            ];
            // Clear buffer and output JSON
            ob_clean();
            echo json_encode($response);
            exit;
        }
        
        // Check if patient data exists for this combination
        $patient_exists = $this->remark_model->validate_patient_data_exists($bahagian_utama, $bulan, $tahun);
        
        if (!$patient_exists) {
            log_message('warning', 'No exact patient match found for remark: ' . $bahagian_utama . ' ' . $bulan . ' ' . $tahun);
        }
        
        // Prepare data for model
        $remark_data = [
            'T08_BAHAGIAN_UTAMA' => $bahagian_utama,
            'T08_BULAN' => $bulan,
            'T08_TAHUN' => $tahun,
            'T08_REMARK' => $remark
        ];
        
        // Save remark using model
        $result = $this->remark_model->save_remark($remark_data);
        
        if ($result) {
            $response = [
                'status' => 'success',
                'message' => 'Remark saved successfully',
                'patient_data_exists' => $patient_exists
            ];
            
            if (!$patient_exists) {
                $response['message'] .= ' (Note: No exact patient data match found for these filters)';
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to save remark to database'
            ];
        }
        
    } catch (Exception $e) {
        log_message('error', 'Save remark error: ' . $e->getMessage());
        
        $response = [
            'status' => 'error',
            'message' => 'Server error: ' . $e->getMessage()
        ];
    }
    
    // Clear buffer and output JSON
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

public function test_remark_model()
{
    header('Content-Type: application/json');
    
    try {
        $this->load->model('remark_model');
        
        // Test if model loads
        $test_data = [
            'T08_BAHAGIAN_UTAMA' => 'Test',
            'T08_BULAN' => '1',
            'T08_TAHUN' => '2024',
            'T08_REMARK' => 'Test remark'
        ];
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Remark model loaded successfully',
            'test_data' => $test_data
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Model error: ' . $e->getMessage()
        ]);
    }
    exit;
}

/**
 * Get existing remark
 */
// Replace your get_remark method with this:
public function get_remark()
{
    // Clear all output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Start fresh output buffer
    ob_start();
    
    try {
        $bahagian_utama = $this->input->post('bahagian_utama');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        
        // Validate input
        if (empty($bahagian_utama) || empty($bulan) || empty($tahun)) {
            $response = [
                'status' => 'error',
                'message' => 'Parameters are required'
            ];
        } else {
            // Get remark using model
            $remark = $this->remark_model->get_remark($bahagian_utama, $bulan, $tahun);
            
            // Check if patient data exists for context
            $patient_exists = $this->remark_model->validate_patient_data_exists($bahagian_utama, $bulan, $tahun);
            
            $response = [
                'status' => 'success',
                'remark' => $remark,
                'patient_data_exists' => $patient_exists
            ];
        }
        
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => 'Error fetching remark: ' . $e->getMessage()
        ];
    }
    
    // Clear buffer and output JSON
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
public function test_clean_output()
{
    // Clear all buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    ob_start();
    
    header('Content-Type: application/json');
    echo json_encode(['test' => 'clean_output', 'success' => true]);
    
    $output = ob_get_contents();
    ob_end_clean();
    
    // If we still get output, there's a deeper issue
    if (!empty($output)) {
        echo "ERROR: Output still exists after cleaning: " . htmlspecialchars($output);
    } else {
        // This should not happen if we cleaned properly
        echo "No output after cleaning - this means the issue is elsewhere";
    }
}
public function debug_output()
{
    // Check if there's any output before our JSON
    ob_start();
    
    // Test what happens when we try to output JSON
    header('Content-Type: application/json');
    echo json_encode(['test' => 'debug', 'time' => date('Y-m-d H:i:s')]);
    
    $output = ob_get_contents();
    ob_end_clean();
    
    if (!empty($output)) {
        echo "There was output before JSON: " . htmlspecialchars($output);
    } else {
        echo "No output before JSON - everything looks good";
    }
}
// Replace your existing test_remark_system method with this improved version
// Add this debug method to your Pesakit controller to see what's being received
public function debug_save_remark()
{
    header('Content-Type: application/json');
    
    try {
        // Log all POST data
        log_message('debug', 'POST data: ' . print_r($_POST, true));
        log_message('debug', 'Raw input: ' . file_get_contents('php://input'));
        
        $bahagian_utama = $this->input->post('bahagian_utama');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $remark = $this->input->post('remark');
        
        echo json_encode([
            'status' => 'debug',
            'received_data' => [
                'bahagian_utama' => $bahagian_utama,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'remark' => $remark
            ],
            'post_array' => $_POST,
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
            'method' => $_SERVER['REQUEST_METHOD'],
            'csrf_token' => $this->security->get_csrf_token_name() . ' = ' . $this->security->get_csrf_hash()
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Debug error: ' . $e->getMessage()
        ]);
    }
    exit;
}
}