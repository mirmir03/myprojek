<?php

class Reject extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("reject_model");
        $this->load->model("reject_notes_model"); // Add notes model
    }
    /*****************************************************************
     * REJECT RECORDS
     ****************************************************************/
    public function listreject()
    {
        $data = $this->reject_model->get_all_rejects();

        $this->template->title("Senarai Reject");
        $this->template->set("data", $data);
        $this->template->render();
    }

     // Delete a reject record
    public function delete($id_reject)
    {
        $this->reject_model->delete_reject($id_reject);
        $this->session->set_flashdata('success', 'Rekod reject berjaya dipadam');
        redirect(module_url("reject/listreject"));
    }

   // Add a new reject record
    public function add()
    {
        $jenis_reject = $this->input->post("jenis_reject");
        $custom_reject = $this->input->post("custom_reject");
        $tarikh = $this->input->post("tarikh");

        if ($jenis_reject === "Lain-lain" && !empty($custom_reject)) {
            $jenis_reject = trim($custom_reject);
        }

        if (empty($jenis_reject) || empty($tarikh)) {
            $this->session->set_flashdata('error', 'Kedua-dua Jenis Reject dan Tarikh diperlukan');
            redirect(module_url("reject/listreject"));
            return;
        }

        try {
            $date_obj = DateTime::createFromFormat('Y-m-d', $tarikh);
            if (!$date_obj) {
                throw new Exception('Format tarikh tidak sah');
            }

            $sql = "INSERT INTO EV_T06_REJECT_ANALYSIS (T06_JENIS_REJECT, T06_TARIKH) 
                    VALUES (:jenis_reject, TO_DATE(:tarikh, 'YYYY-MM-DD'))";
            
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_bind_by_name($stmt, ':jenis_reject', $jenis_reject);
            oci_bind_by_name($stmt, ':tarikh', $tarikh);
            oci_execute($stmt);
                
            $this->session->set_flashdata('success', 'Rekod reject berjaya ditambah');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Ralat berlaku semasa menyimpan rekod: ' . $e->getMessage());
        }
        
        redirect(module_url("reject/listreject"));
    }

     // Show form to add new reject
    public function form_add()
    {
        $this->template->render();
    }

     // Show form to edit existing reject
    public function form_edit($id_reject)
    {
        $reject = $this->db
            ->where("T06_ID_REJECT", $id_reject)
            ->get("EV_T06_REJECT_ANALYSIS")
            ->row();

        if (!$reject) {
            $this->session->set_flashdata('error', 'Rekod tidak dijumpai');
            redirect(module_url("reject/listreject"));
            return;
        }

        if (!empty($reject->T06_TARIKH)) {
            $date_formats = ['Y-m-d H:i:s', 'Y-m-d', 'd-M-y', 'd-M-Y'];
            $formatted_date = null;
            
            foreach ($date_formats as $format) {
                $date_obj = DateTime::createFromFormat($format, $reject->T06_TARIKH);
                if ($date_obj) {
                    $formatted_date = $date_obj->format('Y-m-d');
                    break;
                }
            }
            
            if ($formatted_date) {
                $reject->T06_TARIKH = $formatted_date;
            }
        }

        $this->template->set("reject", $reject);
        $this->template->render();
    }

     //Save edited reject record
    public function save($id_reject)
    {
        $jenis_reject = $this->input->post("jenis_reject");
        $custom_reject = $this->input->post("custom_reject");
        $tarikh = $this->input->post("tarikh");

        if ($jenis_reject === "Lain-lain" && !empty($custom_reject)) {
            $jenis_reject = trim($custom_reject);
        }

        if (empty($jenis_reject) || empty($tarikh)) {
            $this->session->set_flashdata('error', 'Kedua-dua Jenis Reject dan Tarikh diperlukan');
            redirect(module_url("reject/form_edit/$id_reject"));
            return;
        }

        try {
            $existing = $this->db->where('T06_ID_REJECT', $id_reject)->get('EV_T06_REJECT_ANALYSIS')->row();
            if (!$existing) {
                $this->session->set_flashdata('error', 'Rekod tidak dijumpai');
                redirect(module_url("reject/listreject"));
                return;
            }

            $date_obj = DateTime::createFromFormat('Y-m-d', $tarikh);
            if (!$date_obj) {
                throw new Exception('Format tarikh tidak sah');
            }

            $sql = "UPDATE EV_T06_REJECT_ANALYSIS 
                    SET T06_JENIS_REJECT = :jenis_reject, 
                        T06_TARIKH = TO_DATE(:tarikh, 'YYYY-MM-DD') 
                    WHERE T06_ID_REJECT = :id_reject";
            
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_bind_by_name($stmt, ':jenis_reject', $jenis_reject);
            oci_bind_by_name($stmt, ':tarikh', $tarikh);
            oci_bind_by_name($stmt, ':id_reject', $id_reject);
            
            if (!oci_execute($stmt)) {
                $error = oci_error($stmt);
                throw new Exception('Database error: ' . $error['message']);
            }

            $this->session->set_flashdata('success', 'Rekod reject berjaya dikemaskini');
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Ralat berlaku semasa mengemaskini rekod: ' . $e->getMessage());
            redirect(module_url("reject/form_edit/$id_reject"));
            return;
        }
        
        redirect(module_url("reject/listreject"));
    }

    // Show reject analysis graph
    public function graph()
{
    $selected_month = $this->input->get('month');
    $selected_year = $this->input->get('year');

    // Chart Data
    $chart_data = $this->reject_model->get_reject_chart_data($selected_month, $selected_year);
    $labels = [];
    $values = [];

    foreach ($chart_data as $row) {
        $labels[] = $row->T06_JENIS_REJECT;
        $values[] = (int)$row->TOTAL_REJECTS;
    }

    // Notes Data
    $notes = $this->reject_notes_model->get_notes_grouped_by_year(); // For dropdown
    $year_notes = $this->reject_notes_model->get_notes_by_month_year($selected_month, $selected_year); // For display

    // Set data for graph and notes
    $this->template->title("Graf Analisis Reject");
    $this->template->set("chart_labels", json_encode($labels));
    $this->template->set("chart_values", json_encode($values));
    $this->template->set("selected_month", $selected_month);
    $this->template->set("selected_year", $selected_year);

    // Add these two lines
    $this->template->set("notes", $notes);
    $this->template->set("year_notes", $year_notes);

    $this->template->render();
}

    // AJAX - Get chart data
    public function get_chart_data()
    {
        $selected_month = $this->input->post('month');
        $selected_year = $this->input->post('year');
        
        $chart_data = $this->reject_model->get_reject_chart_data($selected_month, $selected_year);
        
        $labels = [];
        $values = [];
        
        foreach ($chart_data as $row) {
            $labels[] = $row->T06_JENIS_REJECT;
            $values[] = (int)$row->TOTAL_REJECTS;
        }
        
        echo json_encode([
            'labels' => $labels,
            'values' => $values,
            'total' => array_sum($values)
        ]);
    }

    //Debug data
    public function debug_data()
    {
        $all_data = $this->reject_model->debug_get_all_data();
        
        echo "<h3>All Data in Database:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Reject Type</th><th>Date</th><th>Month</th><th>Year</th></tr>";
        
        foreach ($all_data as $row) {
            echo "<tr>";
            echo "<td>" . $row['T06_JENIS_REJECT'] . "</td>";
            echo "<td>" . $row['T06_TARIKH'] . "</td>";
            echo "<td>" . $row['MONTH_NUM'] . "</td>";
            echo "<td>" . $row['YEAR_NUM'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Testing Month=1 Filter:</h3>";
        $month_data = $this->reject_model->get_reject_chart_data('1', null);
        
        echo "<table border='1'>";
        echo "<tr><th>Reject Type</th><th>Total</th></tr>";
        
        if (empty($month_data)) {
            echo "<tr><td colspan='2'>NO DATA FOUND</td></tr>";
        } else {
            foreach ($month_data as $row) {
                echo "<tr>";
                echo "<td>" . $row->T06_JENIS_REJECT . "</td>";
                echo "<td>" . $row->TOTAL_REJECTS . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        
        echo "<h3>Check your PHP error logs for detailed debug information</h3>";
    }

    /**********************************************************************
     * NOTES-RELATED FUNCTIONALITY
     * Handles all operations related to reject notes
     **********************************************************************/
    public function notes_management()
{
    $selected_month = $this->input->post('month');
    $selected_year = $this->input->post('year') ?: date('Y');

    $notes = $this->reject_notes_model->get_notes_by_month_year($selected_month, $selected_year);
    $notes_by_year = $this->reject_notes_model->get_notes_grouped_by_year(); // ✅ fixed method

    $this->template->title("Pengurusan Nota Reject");
    $this->template->set("year_notes", $notes);
    $this->template->set("notes", $notes_by_year);
    $this->template->set("selected_year", $selected_year);
    $this->template->render();
}
  
     //Save a new note
    public function save_note() 
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $month = $this->input->post('month') ?: 'Semua Bulan';
        $year = $this->input->post('year') ?: 'Semua Tahun';

        if (is_numeric($month) && $month !== 'Semua Bulan') {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $note_text = $this->input->post('note_text');
        $created_by = $this->session->userdata('username');
        
        if (empty($note_text)) {
            echo json_encode(['success' => false, 'message' => 'Note text cannot be empty']);
            return;
        }
        
        $result = $this->reject_notes_model->save_note($month, $year, $note_text, $created_by);
        
        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Note saved successfully',
                'period' => $month . '-' . $year
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save note']);
        }
    }
    
    
     // Get note for specific period
    public function get_note()
    {
        $month = $this->input->get('month') ?: 'Semua Bulan';
        $year = $this->input->get('year') ?: 'Semua Tahun';

        $note = $this->reject_notes_model->get_note($month, $year);

        echo json_encode([
            'success' => true,
            'note' => $note,
            'period' => $month . '-' . $year
        ]);
    }

     
     // Delete note by ID (updated method)
    public function delete_note()
{
    header('Content-Type: application/json');
    
    try {
        $id = $this->input->post('id');
        if (!$id) {
            throw new Exception('No ID provided');
        }

        // Add debug logging
        log_message('debug', 'Attempting to delete note ID: '.$id);
        
        $result = $this->reject_notes_model->delete_note_by_id($id);
        
        if (!$result) {
            throw new Exception('Delete operation failed');
        }
        
        echo json_encode(['success' => true]);
        
    } catch (Exception $e) {
        log_message('error', 'Delete note error: '.$e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


     // Delete all notes
    public function delete_all_notes()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $result = $this->reject_notes_model->delete_all_notes();
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'All notes deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete notes']);
        }
    }

     // Delete note by month/year
    public function delete_note_by_period()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $month = $this->input->post('month') ?: 'Semua Bulan';
        $year = $this->input->post('year') ?: 'Semua Tahun';
        
        $result = $this->reject_notes_model->delete_note_by_period($month, $year);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete note or note not found']);
        }
    }
    
     //Get all notes
    public function get_all_notes()
    {
        $notes = $this->reject_notes_model->get_all_notes();
        
        echo json_encode([
            'success' => true,
            'notes' => $notes,
            'count' => count($notes)
        ]);
    }
    
     // Page to manage all notes
    public function manage_notes()
    {
        $all_notes = $this->reject_notes_model->get_all_notes();
        $notes_count = $this->reject_notes_model->get_notes_count();
        
        $this->template->title("Manage Notes");
        $this->template->set("all_notes", $all_notes);
        $this->template->set("notes_count", $notes_count);
        $this->template->render();
    }

     //Get note by ID
    public function get_note_by_id()
    {
        $id = $this->input->get('id');
        $note = $this->reject_notes_model->get_note_by_id($id);
        
        echo json_encode([
            'success' => !empty($note),
            'note' => $note
        ]);
    }

    // Update existing note
    public function update_note()
    {
        $id = $this->input->post('id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $note_text = $this->input->post('note_text');
        
        if (empty($note_text)) {
            echo json_encode(['success' => false, 'message' => 'Nota tidak boleh kosong']);
            return;
        }
        
        $result = $this->reject_notes_model->update_note($id, $month, $year, $note_text);
        
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Nota berjaya dikemaskini' : 'Gagal mengemaskini nota'
        ]);
    }
    
}