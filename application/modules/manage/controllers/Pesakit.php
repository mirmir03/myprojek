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
        $this->template->set("data", $data);
        $this->template->render();
    }

    public function delete($id_pesakit, $id2 = "")
{
    // First check if patient has doctor comment
    $patient = $this->db
        ->where("T01_ID_PESAKIT", $id_pesakit)
        ->get("EV_T01_PESAKIT")
        ->row();

    // If record has doctor comment, prevent deletion
    if (!empty($patient->T01_DOCTOR_COMMENT)) {
        $this->session->set_flashdata('error', 'Cannot delete patient record - doctor comment exists');
        redirect(module_url("pesakit/listpesakit"));
        return;
    }

    // Proceed with deletion if no doctor comment
    $this->pesakit_model->delete_pesakit($id_pesakit);
    $this->session->set_flashdata('success', 'Patient record deleted successfully');
    redirect(module_url("pesakit/listpesakit"));
}

    public function get_patient_data()
    {
        $no_rujukan = $this->input->post("no_rujukan");
        $kategori = $this->input->post("kategori");
        
        $result = null;
        
        if ($kategori == 'pelajar' || $kategori == 'staf') {
            // Query to get data from warga table for students or staff
            $this->db->select("T01_NAMA_WARGA as nama");
            $this->db->where("T01_NO_RUJUKAN", $no_rujukan);
            $result = $this->db->get("EV_T01_WARGA")->row();
        }
        
        if ($result) {
            echo json_encode(['status' => 'success', 'data' => $result]);
        } else {
            echo json_encode(['status' => 'not_found']);
        }
    }

    public function add()
    {
        $kategori = $this->input->post("kategori");
        $no_rujukan = $this->input->post("no_rujukan");

        if (!$no_rujukan) {
            $this->session->set_flashdata('error', 'No Rujukan is required');
            redirect(module_url("pesakit/form_add"));
            return;
        }

        // Validate format based on category
        $valid_format = false;
        switch ($kategori) {
            case 'pelajar':
                $valid_format = preg_match('/^s\d+$/i', $no_rujukan);
                break;
            case 'staf':
                $valid_format = preg_match('/^w\d+$/i', $no_rujukan);
                break;
            case 'pesara':
            case 'tanggungan':
            case 'warga luar':
                $valid_format = preg_match('/^\d{6}[-]\d{2}[-]\d{4}$/', $no_rujukan);
                break;
        }

        if (!$valid_format) {
            $this->session->set_flashdata('error', 'Format No Rujukan tidak sah');
            redirect(module_url("pesakit/form_add"));
            return;
        }

        $nama_pesakit = $this->input->post("nama_pesakit");
        $jantina = $this->input->post("jantina");
        
        // For students and staff, try to fetch the name from the database
        if (($kategori == 'pelajar' || $kategori == 'staf') && empty($nama_pesakit)) {
            $this->db->select("T01_NAMA_WARGA as nama");
            $this->db->where("T01_NO_RUJUKAN", $no_rujukan);
            $result = $this->db->get("EV_T01_WARGA")->row();
            
            if ($result) {
                $nama_pesakit = $result->nama;
            } else {
                $this->session->set_flashdata('error', 'No record found for the given reference number');
                redirect(module_url("pesakit/form_add"));
                return;
            }
        }
        
        // Check if all required fields are provided
        if (empty($nama_pesakit)) {
            $this->session->set_flashdata('error', 'Nama Pesakit is required');
            redirect(module_url("pesakit/form_add"));
            return;
        }
        
        // For IC-based categories, determine gender if not already set
        if (($kategori == 'pesara' || $kategori == 'tanggungan' || $kategori == 'warga luar') && empty($jantina)) {
            // Extract gender digit from IC (format: xxxxxx-xx-xxxx)
            $parts = explode("-", $no_rujukan);
            if (count($parts) === 3) {
                $genderDigit = intval($parts[1]);
                // CORRECTED: Odd means female, even means male
                $jantina = ($genderDigit % 2 === 1) ? 'Perempuan' : 'Lelaki';
            }
        }

        $data_to_insert = [
            "T01_NAMA_PESAKIT" => $nama_pesakit,
            "T01_NO_RUJUKAN" => $no_rujukan,
            "T01_JANTINA" => $jantina,
            "T01_KATEGORI" => $kategori,
            "T01_BAHAGIAN_UTAMA" => $this->input->post("bhg_utama"),
            "T01_SUB_BAHAGIAN" => $this->input->post("sub_bhg"),
            "T01_TARIKH" => DateTime::createFromFormat('Y-m-d', $this->input->post("tarikh"))->format('d-M-Y')
        ];

        // Insert data into the patient table
        $this->db->insert("EV_T01_PESAKIT", $data_to_insert);
        $this->session->set_flashdata('success', 'Patient record added successfully');
        redirect(module_url("pesakit/listpesakit"));
    }

    public function form_add()
    {
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

    public function graphs() 
    {
        if ($this->input->get('fetch_data') === 'true') {
            try {
                $startDate = $this->input->get('startDate');
                $endDate = $this->input->get('endDate');
                $kategori = $this->input->get('kategori');

                $data = $this->pesakit_model->getChartData($startDate, $endDate, $kategori);

                if ($data->num_rows() === 0) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(['status' => 'empty']))
                        ->_display();
                    exit;
                }

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data->result_array()))
                    ->_display();
                exit;
            } catch (Exception $e) {
                log_message('error', 'Chart data error: ' . $e->getMessage());
                $this->output
                    ->set_status_header(500)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => $e->getMessage()]))
                    ->_display();
                exit;
            }
        }

        $this->template->title("Laporan Grafik Pesakit");
        $this->template->render();
    }

    public function get_filtered_data() {
        $bulan = $this->input->post('bulan');
        $kategori = $this->input->post('kategori');
        $bhg_utama = $this->input->post('bhg_utama');
        
        $this->db->select("T01_JANTINA, T01_SUB_BAHAGIAN, COUNT(*) as count");
        $this->db->from("EV_T01_PESAKIT");
        
        if ($bulan) {
            $this->db->where("TO_CHAR(TO_DATE(T01_TARIKH, 'DD-MON-YYYY'), 'MM')", $bulan);
        }
        if ($kategori) {
            $this->db->where("T01_KATEGORI", $kategori);
        }
        if ($bhg_utama) {
            $this->db->where("T01_BAHAGIAN_UTAMA", $bhg_utama);
        }
        
        $this->db->group_by("T01_JANTINA, T01_SUB_BAHAGIAN");
        $query = $this->db->get();
        
        echo json_encode($query->result());
    }

    public function grafik() {
        
        
        // OR if using a template library
        $this->template->render('pesakit/grafik_pesakit');
    }

    public function get_patient_stats() {
        $month = $this->input->post('month');
        $category = $this->input->post('category');
        $diagnosis_area = $this->input->post('diagnosis_area');
        
        // Gender count data
        $this->db->select("T01_JANTINA as gender, COUNT(*) as count");
        $this->db->from("EV_T01_PESAKIT");
        
        if ($month) $this->db->where("TO_CHAR(TO_DATE(T01_TARIKH, 'DD-MON-YYYY'), 'MM')", $month);
        if ($category) $this->db->where("T01_KATEGORI", $category);
        if ($diagnosis_area) $this->db->where("T01_BAHAGIAN_UTAMA", $diagnosis_area);
        
        $gender_data = $this->db->group_by("T01_JANTINA")->get()->result();
        
        // Diagnosis by gender data
        $this->db->select("T01_JANTINA as gender, T01_SUB_BAHAGIAN as sub_part, COUNT(*) as count");
        $this->db->from("EV_T01_PESAKIT");
        
        if ($month) $this->db->where("TO_CHAR(TO_DATE(T01_TARIKH, 'DD-MON-YYYY'), 'MM')", $month);
        if ($category) $this->db->where("T01_KATEGORI", $category);
        if ($diagnosis_area) $this->db->where("T01_BAHAGIAN_UTAMA", $diagnosis_area);
        
        $diagnosis_data = $this->db->group_by("T01_JANTINA, T01_SUB_BAHAGIAN")->get()->result();
        
        echo json_encode([
            'gender' => $gender_data,
            'diagnosis' => $diagnosis_data
        ]);
    }
}
?>



 

