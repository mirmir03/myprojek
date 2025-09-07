<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Remark extends Admin_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Remark_model");
    }
public function test() {
    echo "Remark controller is working!";
}
    /**
     * Save remark to database
     */
    public function save_remark()
{
    header('Content-Type: application/json');
    
    try {
        $bahagian_utama = $this->input->post('bahagian_utama');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $remark = $this->input->post('remark');
        
        // Debug logging
        log_message('debug', 'Save remark called with POST data: ' . print_r($_POST, true));
        
        if (empty($bahagian_utama) || empty($bulan) || empty($tahun)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Bahagian Utama, Bulan, and Tahun are required'
            ]);
            exit;
        }
        
        // Get a valid patient ID from the filters
        $patient = $this->db->select('T01_ID_PESAKIT')
                           ->where('T01_BAHAGIAN_UTAMA', $bahagian_utama)
                           ->where('EXTRACT(MONTH FROM T01_TARIKH)', $bulan)
                           ->where('EXTRACT(YEAR FROM T01_TARIKH)', $tahun)
                           ->where('T01_STATUS', 1)
                           ->limit(1)
                           ->get('EV_T01_PESAKIT')->row();
        
        if (!$patient) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No patient found for the selected criteria'
            ]);
            exit;
        }
        
        // Check if remark exists
        $existing = $this->db->where('T08_BAHAGIAN_UTAMA', $bahagian_utama)
                            ->where('T08_BULAN', $bulan)
                            ->where('T08_TAHUN', $tahun)
                            ->get('EV_T08_REMARK')->row();
        
        if ($existing) {
            $this->db->where('T08_ID', $existing->T08_ID)
                    ->update('EV_T08_REMARK', ['T08_REMARK' => $remark]);
        } else {
            $this->db->set('T08_ID', 'EV_T08_REMARK_SEQ.NEXTVAL', false)
                    ->insert('EV_T08_REMARK', [
                        'T08_BAHAGIAN_UTAMA' => $bahagian_utama,
                        'T08_BULAN' => $bulan,
                        'T08_TAHUN' => $tahun,
                        'T08_REMARK' => $remark,
                        'T01_ID_PESAKIT' => $patient->T01_ID_PESAKIT
                    ]);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Remark saved successfully'
        ]);
        
    } catch (Exception $e) {
        // Log error
        log_message('error', 'Save remark error: ' . $e->getMessage());
        log_message('error', 'Database error: ' . $this->db->last_query());
        
        // Return JSON error
        echo json_encode([
            'status' => 'error',
            'message' => 'Error saving remark: ' . $e->getMessage()
        ]);
    }
    exit;
}

    /**
     * Get remarks based on filters
     */
    public function get_remark()
{
    header('Content-Type: application/json');
    
    try {
        $bahagian_utama = $this->input->post('bahagian_utama');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        
        $remark = $this->db->select('T08_REMARK')
                          ->where('T08_BAHAGIAN_UTAMA', $bahagian_utama)
                          ->where('T08_BULAN', $bulan)
                          ->where('T08_TAHUN', $tahun)
                          ->get('EV_T08_REMARK')
                          ->row();
        
        echo json_encode([
            'status' => 'success',
            'remark' => $remark ? $remark->T08_REMARK : ''
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error fetching remark: ' . $e->getMessage()
        ]);
    }
    exit;
}

    /**
     * Get latest remark for current filters
     */
    public function get_latest_remark()
    {
        // Check if it's an AJAX request
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed', 403);
        }

        $response = array('status' => 'error', 'message' => 'Failed to fetch remark');

        try {
            // Get filter parameters
            $bahagian_utama = $this->input->post('bahagian_utama');
            $month = $this->input->post('month');
            $year = $this->input->post('year');

            $filters = array();
            if (!empty($bahagian_utama)) $filters['T08_BAHAGIAN_UTAMA'] = $bahagian_utama;
            if (!empty($month)) $filters['T08_BULAN'] = $month;
            if (!empty($year)) $filters['T08_TAHUN'] = $year;

            // Get latest remark
            $remark = $this->Remark_model->get_latest_remark($filters);

            $response['status'] = 'success';
            $response['message'] = 'Remark fetched successfully';
            $response['data'] = $remark;

        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }

        echo json_encode($response);
    }

    /**
     * Test method to verify AJAX is working
     */
    public function test_ajax()
    {
        header('Content-Type: application/json');
        
        $bahagian_utama = $this->input->post("bahagian_utama");
        $month = $this->input->post("month");
        $year = $this->input->post("year");
        
        echo json_encode([
            'status' => 'success',
            'message' => 'AJAX is working',
            'received_data' => [
                'bahagian_utama' => $bahagian_utama,
                'month' => $month,
                'year' => $year
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        
        exit;
    }

    /**
     * Debug method to test remark functionality
     */
    public function debug_remark_data()
    {
        header('Content-Type: application/json');
        
        try {
            // Debug query to see all remarks
            $debug_sql = "SELECT 
                            T08_ID,
                            T08_REMARK,
                            T08_BAHAGIAN_UTAMA,
                            T08_BULAN,
                            T08_TAHUN,
                            created_at,
                            created_by
                          FROM EV_T08_REMARK
                          ORDER BY created_at DESC";

            $stmt = oci_parse($this->db->conn_id, $debug_sql);
            oci_execute($stmt);

            $debug_data = array();
            while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
                $debug_data[] = $row;
            }

            oci_free_statement($stmt);

            echo json_encode([
                'status' => 'debug',
                'all_remarks_count' => count($debug_data),
                'sample_data' => array_slice($debug_data, 0, 5), // First 5 records
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * Test table structure
     */
    public function test_table_structure()
    {
        header('Content-Type: application/json');
        
        try {
            // Test if table exists and get structure
            $sql = "SELECT COLUMN_NAME, DATA_TYPE 
                    FROM USER_TAB_COLUMNS 
                    WHERE TABLE_NAME = 'EV_T08_REMARK'
                    ORDER BY COLUMN_ID";
            
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_execute($stmt);

            $columns = array();
            while (($row = oci_fetch_array($stmt, OCI_ASSOC))) {
                $columns[] = $row;
            }

            // Also get a sample record
            $sample_sql = "SELECT * FROM EV_T08_REMARK WHERE ROWNUM <= 1";
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
    public function test_self() {
    $this->load->model("Remark_model");
    echo "Remark controller and model working!";
    
    // Test simple insert
    $test_data = array(
        'T08_REMARK' => 'Test remark from Remark controller',
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => 1
    );
    
    $result = $this->Remark_model->insert_remark($test_data);
    echo "<br>Insert result: " . ($result ? "Success ID: $result" : "Failed");
}
}
?>