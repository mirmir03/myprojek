<?php

class Pesakit extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("pesakit_model");
    }

    public function listpesakit()
{
    $data = $this->pesakit_model->get_all_pesakit();
    
    $this->template->title("Senarai pesakit");
    $this->template->set_breadcrumb("Senarai Pesakit");
    $this->template->set("data", $data);
    $this->template->render();
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

   // FIXED GRAPH METHODS
    public function patient_graph()
    {
        $this->template->title("Patient Statistics Graph");
        $this->template->render();
    }

    public function get_graph_data()
    {
        // Set proper headers for JSON response
        header('Content-Type: application/json');
        
        // Get POST data
        $bahagian_utama = $this->input->post('bahagian_utama');
        $kategori = $this->input->post('kategori');
        $month = $this->input->post('month');
        $year = $this->input->post('year');

        // Validate required fields
        if (empty($bahagian_utama) || empty($kategori)) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Bahagian Utama and Kategori are required'
            ]);
            exit;
        }

        try {
            // Get data from model
            $data = $this->pesakit_model->get_graph_data($bahagian_utama, $kategori, $month, $year);
            
            // Return success response
            echo json_encode([
                'status' => 'success', 
                'data' => $data
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
        
        // Important: Exit to prevent any additional output
        exit;
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

    
    
}
?>