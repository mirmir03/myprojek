<?php

class Reject extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("reject_model");
    }

    public function listreject()
    {
        $data = $this->reject_model->get_all_rejects();

        $this->template->title("Senarai Reject");
        $this->template->set("data", $data);
        $this->template->render();
    }

    // New method for graph page
    public function graph()
    {
        // Get filter parameters
        $selected_month = $this->input->get('month');
        $selected_year = $this->input->get('year');
        
        // Set default to current year if no filters
        if (empty($selected_month) && empty($selected_year)) {
            $selected_year = date('Y');
        }
        
        // Get chart data based on filters
        $chart_data = $this->reject_model->get_reject_chart_data($selected_month, $selected_year);
        
        // Prepare data for chart
        $labels = [];
        $values = [];
        
        foreach ($chart_data as $row) {
            $labels[] = $row->T06_JENIS_REJECT;
            $values[] = (int)$row->TOTAL_REJECTS;
        }
        
        $this->template->title("Graf Analisis Reject");
        $this->template->set("chart_labels", json_encode($labels));
        $this->template->set("chart_values", json_encode($values));
        $this->template->set("selected_month", $selected_month);
        $this->template->set("selected_year", $selected_year);
        $this->template->render();
    }

    // AJAX method to get chart data
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

    public function delete($id_reject)
    {
        // Simple delete - just like Pesakit controller
        $this->reject_model->delete_reject($id_reject);
        $this->session->set_flashdata('success', 'Rekod reject berjaya dipadam');
        redirect(module_url("reject/listreject"));
    }

    public function add()
    {
        $jenis_reject = $this->input->post("jenis_reject");
        $custom_reject = $this->input->post("custom_reject");
        $tarikh = $this->input->post("tarikh");

        // Use custom reject if "Lain-lain" is selected
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

            // Use TO_DATE function in the SQL
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

    public function form_add()
    {
        $this->template->render();
    }

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

        // Format the date for display in HTML date input
        if (!empty($reject->T06_TARIKH)) {
            // Handle different possible date formats from Oracle
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

    public function save($id_reject)
    {
        $jenis_reject = $this->input->post("jenis_reject");
        $custom_reject = $this->input->post("custom_reject");
        $tarikh = $this->input->post("tarikh");

        // Use custom reject if "Lain-lain" is selected
        if ($jenis_reject === "Lain-lain" && !empty($custom_reject)) {
            $jenis_reject = trim($custom_reject);
        }

        if (empty($jenis_reject) || empty($tarikh)) {
            $this->session->set_flashdata('error', 'Kedua-dua Jenis Reject dan Tarikh diperlukan');
            redirect(module_url("reject/form_edit/$id_reject"));
            return;
        }

        try {
            // Check if record exists
            $existing = $this->db->where('T06_ID_REJECT', $id_reject)->get('EV_T06_REJECT_ANALYSIS')->row();
            if (!$existing) {
                $this->session->set_flashdata('error', 'Rekod tidak dijumpai');
                redirect(module_url("reject/listreject"));
                return;
            }

            // Validate date format
            $date_obj = DateTime::createFromFormat('Y-m-d', $tarikh);
            if (!$date_obj) {
                throw new Exception('Format tarikh tidak sah');
            }

            // Use Oracle TO_DATE function for proper date handling
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
}