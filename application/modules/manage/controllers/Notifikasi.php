<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("notifikasi_model");
    }

    public function index()
    {
        $data = $this->notifikasi_model->get_all_notifikasi();
        $this->template->title("Senarai Notifikasi Pesanan");
        $this->template->set("data", $data);
        $this->template->render();
    }

    public function tambah()
    {
        $this->template->title("Tambah Notifikasi");
        $this->template->render();
    }

    public function simpan()
    {
        // Upload config
        $config['upload_path']   = './uploads/pdf/';
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 2048; // 2MB max
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        $pdf_file_name = null;

        if (!empty($_FILES['pdf_file']['name'])) {
            if (!$this->upload->do_upload('pdf_file')) {
                $errors = $this->upload->display_errors('', '');
                $this->session->set_flashdata('error', 'Ralat upload fail: ' . $errors);
                redirect(module_url("notifikasi/tambah"));
                return;
            } else {
                $upload_data = $this->upload->data();
                // Use original name with spaces
                $original_filename = str_replace('_', ' ', $upload_data['orig_name']);
                // Rename uploaded file to original name
                rename(
                    $upload_data['full_path'],
                    $upload_data['file_path'] . $original_filename
                );
                $pdf_file_name = $original_filename;
            }
        }

        // Get raw input dates
        $tarikh_pesanan_raw = $this->input->post("tarikh_pesanan");
        $tamat_pesanan_raw = $this->input->post("tamat_pesanan");

        try {
            $pesanan = new DateTime($tarikh_pesanan_raw);
            $tamat = new DateTime($tamat_pesanan_raw);
            $today = new DateTime();

            // Check 3-day minimum condition (was 14 in original, changed to 3 as per requirement)
            $diff = $pesanan->diff($tamat)->days;
            if ($tamat <= $pesanan || $diff < 3) {
                $this->session->set_flashdata('error', 'Tarikh Tamat mestilah sekurang-kurangnya 3 hari selepas Tarikh Pesanan.');
                redirect(module_url("notifikasi/tambah"));
                return;
            }

            // Convert dates to Oracle-friendly format
            $tarikh_pesanan = $pesanan->format('d-M-Y');
            $tamat_pesanan = $tamat->format('d-M-Y');
            $tarikh_semasa = $today->format('d-M-Y');

        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Format tarikh tidak sah.');
            redirect(module_url("notifikasi/tambah"));
            return;
        }

        $input = [
            "T06_NAMA_PEMBEKAL"    => $this->input->post("nama_pembekal"),
            "T06_NOMBOR_TELEFON"   => $this->input->post("nombor_telefon"),
            "T06_NOMBOR_PESANAN"   => $this->input->post("nombor_pesanan"),
            "T06_TARIKH_PESANAN"   => $tarikh_pesanan,
            "T06_TAMAT_PESANAN"    => $tamat_pesanan,
            "T06_JUMLAH_HARGA"     => $this->input->post("jumlah_harga"),
            "T06_PDF_FILE"         => $pdf_file_name,
            "T06_TARIKH"           => $tarikh_semasa,
            "T06_STATUS"           => null // Initialize as null (Belum Terima)
        ];

        if ($this->notifikasi_model->insert_notifikasi($input)) {
            $this->session->set_flashdata('success', 'Notifikasi berjaya ditambah.');
        } else {
            $this->session->set_flashdata('error', 'Ralat semasa menyimpan notifikasi.');
        }
        
        redirect(module_url("notifikasi"));
    }

    public function mark_received_ajax($id)
    {
        // Set JSON header first
        header('Content-Type: application/json');
        
        // Validate input
        if (empty($id) || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak sah']);
            exit;
        }

        $this->load->database();

        // Get current notification
        $this->db->where('T06_ID_NOTIFIKASI', $id);
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        $notif = $query->row();

        if (!$notif) {
            echo json_encode(['success' => false, 'message' => 'Notifikasi tidak dijumpai']);
            exit;
        }

        // Toggle status logic
        $new_status = ($notif->T06_STATUS == 'Received') ? null : 'Received';

        // Update the status
        $this->db->where('T06_ID_NOTIFIKASI', $id);
        $updated = $this->db->update('EV_T07_NOTIFIKASI_PESANAN', ['T06_STATUS' => $new_status]);

        if ($updated) {
            // Get updated count using model method
            $unreceived_count = $this->notifikasi_model->count_sidebar_notifikasi();

            echo json_encode([
                'success' => true,
                'new_status' => $new_status,
                'new_status_text' => ($new_status == 'Received') ? 'Diterima' : 'Belum Terima',
                'count' => $unreceived_count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kemas kini gagal']);
        }
        
        exit;
    }

    public function delete($id_notifikasi, $id2 = "")
    {
        // First check if notification exists
        $notifikasi = $this->notifikasi_model->get_notifikasi_by_id($id_notifikasi);
        
        if (!$notifikasi) {
            $this->session->set_flashdata('error', 'Notifikasi tidak dijumpai.');
            redirect(module_url("notifikasi"));
            return;
        }

        // Check if notification has been received - prevent deletion if received
        if ($notifikasi->T06_STATUS == 'Received') {
            $this->session->set_flashdata('error', 'Tidak boleh padam notifikasi yang telah diterima.');
            redirect(module_url("notifikasi"));
            return;
        }

        // Delete PDF file if exists
        if (!empty($notifikasi->T06_PDF_FILE) && file_exists('./uploads/pdf/' . $notifikasi->T06_PDF_FILE)) {
            unlink('./uploads/pdf/' . $notifikasi->T06_PDF_FILE);
        }

        // Proceed with deletion
        if ($this->notifikasi_model->delete_notifikasi($id_notifikasi)) {
            $this->session->set_flashdata('success', 'Notifikasi berjaya dipadam.');
        } else {
            $this->session->set_flashdata('error', 'Ralat semasa memadam notifikasi.');
        }

        redirect(module_url("notifikasi"));
    }

    public function edit($id)
    {
        $data = $this->notifikasi_model->get_notifikasi_by_id($id);
        
        if (!$data) {
            $this->session->set_flashdata('error', 'Notifikasi tidak dijumpai.');
            redirect(module_url("notifikasi"));
        }

        $this->template->title("Edit Notifikasi");
        $this->template->set("data", $data);
        $this->template->render();
    }

    public function update($id)
    {
        $notifikasi = $this->notifikasi_model->get_notifikasi_by_id($id);
        
        if (!$notifikasi) {
            $this->session->set_flashdata('error', 'Notifikasi tidak dijumpai.');
            redirect(module_url("notifikasi"));
        }

        // Handle file upload
        $pdf_file_name = $notifikasi->T06_PDF_FILE;
        
        if (!empty($_FILES['pdf_file']['name'])) {
            $config['upload_path'] = './uploads/pdf/';
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('pdf_file')) {
                // Delete old file if exists
                if (!empty($notifikasi->T06_PDF_FILE) && file_exists('./uploads/pdf/' . $notifikasi->T06_PDF_FILE)) {
                    unlink('./uploads/pdf/' . $notifikasi->T06_PDF_FILE);
                }
                
                $upload_data = $this->upload->data();
                $original_filename = str_replace('_', ' ', $upload_data['orig_name']);
                rename($upload_data['full_path'], $upload_data['file_path'] . $original_filename);
                $pdf_file_name = $original_filename;
            }
        }

        // Handle dates with validation
        try {
            $tarikh_pesanan = new DateTime($this->input->post("tarikh_pesanan"));
            $tamat_pesanan = new DateTime($this->input->post("tamat_pesanan"));
            
            // Validate date difference
            $diff = $tarikh_pesanan->diff($tamat_pesanan)->days;
            if ($tamat_pesanan <= $tarikh_pesanan || $diff < 3) {
                $this->session->set_flashdata('error', 'Tarikh Tamat mestilah sekurang-kurangnya 3 hari selepas Tarikh Pesanan.');
                redirect(module_url("notifikasi/edit/" . $id));
                return;
            }
            
            $tarikh_pesanan_formatted = $tarikh_pesanan->format('d-M-Y');
            $tamat_pesanan_formatted = $tamat_pesanan->format('d-M-Y');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Format tarikh tidak sah.');
            redirect(module_url("notifikasi/edit/" . $id));
            return;
        }

        // Prepare update data
        $update_data = [
            "T06_NAMA_PEMBEKAL" => $this->input->post("nama_pembekal"),
            "T06_NOMBOR_TELEFON" => $this->input->post("nombor_telefon"),
            "T06_NOMBOR_PESANAN" => $this->input->post("nombor_pesanan"),
            "T06_JUMLAH_HARGA" => $this->input->post("jumlah_harga"),
            "T06_PDF_FILE" => $pdf_file_name,
            "T06_TARIKH_PESANAN" => $tarikh_pesanan_formatted,
            "T06_TAMAT_PESANAN" => $tamat_pesanan_formatted
        ];

        if ($this->notifikasi_model->update_notifikasi($id, $update_data)) {
            $this->session->set_flashdata('success', 'Notifikasi berjaya dikemaskini.');
        } else {
            $this->session->set_flashdata('error', 'Ralat semasa mengemaskini notifikasi.');
        }

        redirect(module_url("notifikasi"));
    }
    
    // API endpoint for getting notification count (for AJAX polling)
    public function get_notification_count()
{
    header('Content-Type: application/json');
    $count = $this->notifikasi_model->count_sidebar_notifikasi();
    echo json_encode([
        'success' => true, 
        'count' => $count,
        'timestamp' => time() // For cache busting
    ]);
    exit;
}

    public function debug_badge()
{
    $this->load->model('notifikasi_model');
    
    echo "<h3>Current Database Data:</h3>";
    $all_data = $this->db->get('EV_T07_NOTIFIKASI_PESANAN')->result();
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Pembekal</th><th>Status</th><th>Tamat Pesanan</th><th>Days Left</th></tr>";
    
    $today = new DateTime();
    foreach ($all_data as $row) {
        $deadline = new DateTime($row->T06_TAMAT_PESANAN);
        $days_left = $today->diff($deadline)->days;
        $is_past = $today > $deadline;
        
        echo "<tr>";
        echo "<td>{$row->T06_ID_NOTIFIKASI}</td>";
        echo "<td>{$row->T06_NAMA_PEMBEKAL}</td>";
        echo "<td>" . ($row->T06_STATUS ?: 'NULL') . "</td>";
        echo "<td>" . $deadline->format('Y-m-d') . "</td>";
        echo "<td>" . ($is_past ? "-" : "") . $days_left . " days</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Count Results:</h3>";
    echo "<p>Sidebar count: " . $this->notifikasi_model->count_sidebar_notifikasi() . "</p>";
    echo "<p>Basic unreceived count: " . $this->notifikasi_model->get_unreceived_count() . "</p>";
    
    // Test the exact SQL
    $today = date('Y-m-d');
    $sql = "
        SELECT COUNT(*) as notification_count
        FROM EV_T07_NOTIFIKASI_PESANAN
        WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
        AND TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3)
        AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN
    ";
    
    $query = $this->db->query($sql, [$today, $today]);
    $result = $query->row();
    
    echo "<h3>SQL Debug:</h3>";
    echo "<p>Today: $today</p>";
    echo "<p>Last query: " . $this->db->last_query() . "</p>";
    echo "<p>SQL result: " . $result->NOTIFICATION_COUNT . "</p>";
}
}