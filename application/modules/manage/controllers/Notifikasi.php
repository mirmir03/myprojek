<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("notifikasi_model");
    }

    /**
     * Display all notifications
     */
    public function index()
    {
        $data = $this->notifikasi_model->get_all_notifikasi();
        $this->template->title("Senarai Notifikasi Pesanan");
        $this->template->set("data", $data);
        $this->template->render();
    }

    /**
     * Show add notification form
     */
    public function tambah()
    {
        $this->template->title("Tambah Notifikasi");
        $this->template->render();
    }

    /**
     * Save new notification
     */
    public function simpan()
    {
        // Handle file upload
        $pdf_file_name = $this->_handle_pdf_upload();
        if ($pdf_file_name === false) {
            return; // Error already handled in upload function
        }

        // Validate and format dates
        $date_result = $this->_validate_and_format_dates();
        if (!$date_result['success']) {
            $this->session->set_flashdata('error', $date_result['error']);
            redirect(module_url("notifikasi/tambah"));
            return;
        }

        $input = [
            "T06_NAMA_PEMBEKAL"    => $this->input->post("nama_pembekal"),
            "T06_NOMBOR_TELEFON"   => $this->input->post("nombor_telefon"),
            "T06_NOMBOR_PESANAN"   => $this->input->post("nombor_pesanan"),
            "T06_TARIKH_PESANAN"   => $date_result['tarikh_pesanan'],
            "T06_TAMAT_PESANAN"    => $date_result['tamat_pesanan'],
            "T06_JUMLAH_HARGA"     => $this->input->post("jumlah_harga"),
            "T06_PDF_FILE"         => $pdf_file_name,
            "T06_TARIKH"           => $date_result['tarikh_semasa'],
            "T06_STATUS"           => null
        ];

        if ($this->notifikasi_model->insert_notifikasi($input)) {
            $this->session->set_flashdata('success', 'Notifikasi berjaya ditambah.');
        } else {
            $this->session->set_flashdata('error', 'Ralat semasa menyimpan notifikasi.');
        }
        
        redirect(module_url("notifikasi"));
    }

    /**
     * Show edit notification form
     */
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

    /**
     * Update notification
     */
    public function update($id)
    {
        $notifikasi = $this->notifikasi_model->get_notifikasi_by_id($id);
        
        if (!$notifikasi) {
            $this->session->set_flashdata('error', 'Notifikasi tidak dijumpai.');
            redirect(module_url("notifikasi"));
        }

        // Handle file upload if new file provided
        $pdf_file_name = $this->_handle_pdf_upload_for_update($notifikasi);
        
        // Validate and format dates
        $date_result = $this->_validate_and_format_dates();
        if (!$date_result['success']) {
            $this->session->set_flashdata('error', $date_result['error']);
            redirect(module_url("notifikasi/edit/" . $id));
            return;
        }

        $update_data = [
            "T06_NAMA_PEMBEKAL"    => $this->input->post("nama_pembekal"),
            "T06_NOMBOR_TELEFON"   => $this->input->post("nombor_telefon"),
            "T06_NOMBOR_PESANAN"   => $this->input->post("nombor_pesanan"),
            "T06_JUMLAH_HARGA"     => $this->input->post("jumlah_harga"),
            "T06_PDF_FILE"         => $pdf_file_name,
            "T06_TARIKH_PESANAN"   => $date_result['tarikh_pesanan'],
            "T06_TAMAT_PESANAN"    => $date_result['tamat_pesanan']
        ];

        if ($this->notifikasi_model->update_notifikasi($id, $update_data)) {
            $this->session->set_flashdata('success', 'Notifikasi berjaya dikemaskini.');
        } else {
            $this->session->set_flashdata('error', 'Ralat semasa mengemaskini notifikasi.');
        }

        redirect(module_url("notifikasi"));
    }

    /**
     * Delete notification
     */
    public function delete($id_notifikasi)
    {
        $notifikasi = $this->notifikasi_model->get_notifikasi_by_id($id_notifikasi);
        
        if (!$notifikasi) {
            $this->session->set_flashdata('error', 'Notifikasi tidak dijumpai.');
            redirect(module_url("notifikasi"));
            return;
        }

        // Prevent deletion of received notifications
        if ($notifikasi->T06_STATUS == 'Received') {
            $this->session->set_flashdata('error', 'Tidak boleh padam notifikasi yang telah diterima.');
            redirect(module_url("notifikasi"));
            return;
        }

        // Delete associated PDF file
        $this->_delete_pdf_file($notifikasi->T06_PDF_FILE);

        if ($this->notifikasi_model->delete_notifikasi($id_notifikasi)) {
            $this->session->set_flashdata('success', 'Notifikasi berjaya dipadam.');
        } else {
            $this->session->set_flashdata('error', 'Ralat semasa memadam notifikasi.');
        }

        redirect(module_url("notifikasi"));
    }

    /**
     * Toggle notification status via AJAX
     */
    public function mark_received_ajax($id)
    {
        header('Content-Type: application/json');
        
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

        // Toggle status
        $new_status = ($notif->T06_STATUS == 'Received') ? null : 'Received';

        $this->db->where('T06_ID_NOTIFIKASI', $id);
        $updated = $this->db->update('EV_T07_NOTIFIKASI_PESANAN', ['T06_STATUS' => $new_status]);

        if ($updated) {
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

    /**
     * Get notification count for AJAX polling
     */
    public function get_notification_count()
    {
        try {
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $count = $this->notifikasi_model->count_sidebar_notifikasi();
            
            echo json_encode([
                'success' => true, 
                'count' => $count,
                'timestamp' => time(),
                'server_time' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'count' => 0,
                'error' => $e->getMessage(),
                'timestamp' => time()
            ]);
        }
        exit;
    }

    // =============================================================================
    // DEBUG METHODS (Remove in production)
    // =============================================================================

    public function debug()
    {
        $this->load->model('notifikasi_model');
        
        echo "<h3>Debug Notification Counting Logic</h3>";
        
        $debug_data = $this->notifikasi_model->debug_date_calculations();
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>ID</th><th>Pembekal</th><th>Tarikh Pesanan</th><th>Tamat Pesanan</th>
                <th>Status</th><th>Order→Deadline Days</th><th>Days Until Deadline</th><th>Alert Reason</th>
              </tr>";
        
        foreach ($debug_data as $row) {
            echo "<tr>";
            echo "<td>{$row->T06_ID_NOTIFIKASI}</td>";
            echo "<td>{$row->T06_NAMA_PEMBEKAL}</td>";
            echo "<td>" . date('Y-m-d', strtotime($row->T06_TARIKH_PESANAN)) . "</td>";
            echo "<td>" . date('Y-m-d', strtotime($row->T06_TAMAT_PESANAN)) . "</td>";
            echo "<td>" . ($row->T06_STATUS ?: 'NULL') . "</td>";
            echo "<td>{$row->ORDER_TO_DEADLINE_DAYS}</td>";
            echo "<td>{$row->DAYS_UNTIL_DEADLINE}</td>";
            echo "<td>{$row->ALERT_REASON}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Count Results:</h3>";
        echo "<p>Sidebar count: " . $this->notifikasi_model->count_sidebar_notifikasi() . "</p>";
        echo "<p>Basic unreceived count: " . $this->notifikasi_model->get_unreceived_count() . "</p>";
        
        exit;
    }

    public function debug_console()
    {
        $this->load->model('notifikasi_model');
        
        header('Content-Type: application/json');
        
        $debug_data = $this->notifikasi_model->debug_date_calculations();
        $sidebar_count = $this->notifikasi_model->count_sidebar_notifikasi();
        $basic_count = $this->notifikasi_model->get_unreceived_count();
        
        echo json_encode([
            'success' => true,
            'sidebar_count' => $sidebar_count,
            'basic_count' => $basic_count,
            'debug_data' => $debug_data,
            'today' => date('Y-m-d')
        ]);
        
        exit;
    }

    public function test_polling()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => $this->notifikasi_model->count_sidebar_notifikasi(),
            'server_time' => date('Y-m-d H:i:s'),
            'message' => 'Polling test successful'
        ]);
        exit;
    }

    // =============================================================================
    // PRIVATE HELPER METHODS
    // =============================================================================

    /**
     * Handle PDF file upload
     */
    private function _handle_pdf_upload()
    {
        if (empty($_FILES['pdf_file']['name'])) {
            return null;
        }

        $config = [
            'upload_path'   => './uploads/pdf/',
            'allowed_types' => 'pdf',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('pdf_file')) {
            $errors = $this->upload->display_errors('', '');
            $this->session->set_flashdata('error', 'Ralat upload fail: ' . $errors);
            redirect(module_url("notifikasi/tambah"));
            return false;
        }

        $upload_data = $this->upload->data();
        $original_filename = str_replace('_', ' ', $upload_data['orig_name']);
        
        // Rename to original filename
        rename($upload_data['full_path'], $upload_data['file_path'] . $original_filename);
        
        return $original_filename;
    }

    /**
     * Handle PDF upload for update (with old file deletion)
     */
    private function _handle_pdf_upload_for_update($notifikasi)
    {
        if (empty($_FILES['pdf_file']['name'])) {
            return $notifikasi->T06_PDF_FILE;
        }

        $config = [
            'upload_path'   => './uploads/pdf/',
            'allowed_types' => 'pdf',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE
        ];

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('pdf_file')) {
            // Delete old file
            $this->_delete_pdf_file($notifikasi->T06_PDF_FILE);
            
            $upload_data = $this->upload->data();
            $original_filename = str_replace('_', ' ', $upload_data['orig_name']);
            rename($upload_data['full_path'], $upload_data['file_path'] . $original_filename);
            
            return $original_filename;
        }

        return $notifikasi->T06_PDF_FILE;
    }

    /**
     * Delete PDF file from filesystem
     */
    private function _delete_pdf_file($filename)
    {
        if (!empty($filename) && file_exists('./uploads/pdf/' . $filename)) {
            unlink('./uploads/pdf/' . $filename);
        }
    }

    /**
     * Validate and format dates
     */
    private function _validate_and_format_dates()
    {
        $tarikh_pesanan_raw = $this->input->post("tarikh_pesanan");
        $tamat_pesanan_raw = $this->input->post("tamat_pesanan");

        try {
            $pesanan = new DateTime($tarikh_pesanan_raw);
            $tamat = new DateTime($tamat_pesanan_raw);
            $today = new DateTime();

            // Check minimum 3-day requirement
            $diff = $pesanan->diff($tamat)->days;
            if ($tamat <= $pesanan || $diff < 3) {
                return [
                    'success' => false,
                    'error' => 'Tarikh Tamat mestilah sekurang-kurangnya 3 hari selepas Tarikh Pesanan.'
                ];
            }

            return [
                'success' => true,
                'tarikh_pesanan' => $pesanan->format('d-M-Y'),
                'tamat_pesanan' => $tamat->format('d-M-Y'),
                'tarikh_semasa' => $today->format('d-M-Y')
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Format tarikh tidak sah.'
            ];
        }
    }
}