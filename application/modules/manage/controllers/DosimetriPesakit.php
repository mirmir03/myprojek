<?php
class DosimetriPesakit extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Dosimetripesakit_model");
    }

    // List all patients with dosimetry records
    public function listdospesakit()
    {
        // Initialize $data array
        $data = array();
        $data['dosimetri'] = $this->Dosimetripesakit_model->get_all_dosimetri();
        
        $this->template->title("Senarai Dosimetri Pesakit");
        $this->template->set("data", $data['dosimetri']);
        $this->template->render("dosimetri/listdospesakit");
    }

    // Show patient selection page
    public function form_add_pesakit()
    {
        // Load all patients for selection
        $data['pesakit'] = $this->db->get("EV_T01_PESAKIT")->result();
        
        $this->template->title("Pilih Pesakit");
        $this->template->set("pesakit", $data['pesakit']);
        $this->template->render("dosimetri/pilih_pesakit");
    }

    // Show add form for specific patient
    public function form_add($id_pesakit)
    {
        // Verify patient exists
        $pesakit = $this->db
            ->where("T01_ID_PESAKIT", $id_pesakit)
            ->get("EV_T01_PESAKIT")
            ->row();

        if (!$pesakit) {
            $this->session->set_flashdata('error', 'Pesakit tidak dijumpai');
            redirect(module_url("dosimetriPesakit/listdospesakit"));
        }

        $this->template->title("Tambah Rekod Dosimetri");
        $this->template->set("pesakit", $pesakit);
        $this->template->render("dosimetri/form_add_pesakit");
    }

    // Process form submission
    public function add($id_pesakit = null) 
    {
        try {
            // Debug logging
            log_message('debug', 'DosimetriPesakit/add called with ID: '.$id_pesakit);
            log_message('debug', 'POST data: '.print_r($this->input->post(), true));

            // Get patient ID from URL or POST
            $id_pesakit = $id_pesakit ?: $this->input->post('T01_ID_PESAKIT');
            
            if (empty($id_pesakit)) {
                throw new Exception('ID Pesakit tidak ditemui');
            }

            // Verify patient exists
            $patient = $this->db
                ->where("T01_ID_PESAKIT", $id_pesakit)
                ->get("EV_T01_PESAKIT")
                ->row();

            if (!$patient) {
                throw new Exception('Pesakit tidak dijumpai dengan ID: '.$id_pesakit);
            }

            // Validate input
            $this->load->library('form_validation');
            $this->form_validation->set_rules('tube_voltage', 'Voltan Tiub', 'required|numeric');
            $this->form_validation->set_rules('current_time_product', 'Arus-Masa', 'required|numeric');
            $this->form_validation->set_rules('exposure_time', 'Masa Pendedahan', 'required|numeric');
            $this->form_validation->set_rules('source_image_distance', 'Jarak Sumber-Gambar', 'required|numeric');
            $this->form_validation->set_rules('source_skin_distance', 'Jarak Sumber-Kulit', 'required|numeric');
            $this->form_validation->set_rules('collimation_field_size', 'Saiz Medan Kolimasi', 'required');
            $this->form_validation->set_rules('grid', 'Grid', 'required|in_list[Ya,Tidak]');
            $this->form_validation->set_rules('dose_area_product', 'DAP', 'required|numeric');
            $this->form_validation->set_rules('exposure_index', 'Indeks Pendedahan', 'required|numeric');

            if ($this->form_validation->run() == FALSE) {
                throw new Exception(validation_errors());
            }

            // Prepare data with additional validation
            $data = [
                "T01_ID_PESAKIT" => $id_pesakit,
                "T03_TUBE_VOLTAGE" => (float)$this->input->post("tube_voltage"),
                "T03_CURRENT_TIME_PRODUCT" => (float)$this->input->post("current_time_product"),
                "T03_EXPOSURE_TIME" => (float)$this->input->post("exposure_time"),
                "T03_SOURCE_IMAGE_DISTANCE" => (float)$this->input->post("source_image_distance"),
                "T03_SOURCE_SKIN_DISTANCE" => (float)$this->input->post("source_skin_distance"),
                "T03_COLLIMATION_FIELD_SIZE" => $this->input->post("collimation_field_size"),
                "T03_GRID" => $this->input->post("grid"),
                "T03_DOSE_AREA_PRODUCT" => (float)$this->input->post("dose_area_product"),
                "T03_EXPOSURE_INDEX" => (float)$this->input->post("exposure_index"),
            ];

            // Debug data before insert (MOVED AFTER $data is defined)
            log_message('debug', 'Data being inserted: '.print_r($data, true));

            // Insert data
            $insert_result = $this->Dosimetripesakit_model->create($data);
            
            // Debug insert result
            log_message('debug', 'Insert result: '.($insert_result ? 'SUCCESS' : 'FAILED'));
            
            if (!$insert_result) {
                $error = $this->db->error();
                throw new Exception('Gagal menambah rekod dosimetri: '.$error['message']);
            }

            log_message('info', 'Successfully added dosimetry record for patient ID: '.$id_pesakit);
            $this->session->set_flashdata('success', 'Rekod dosimetri berjaya ditambah');

        } catch (Exception $e) {
            log_message('error', 'Error in DosimetriPesakit/add: '.$e->getMessage());
            $this->session->set_flashdata('error', $e->getMessage());
            
            // Redirect back to form with patient ID if available
            if (!empty($id_pesakit)) {
                redirect(module_url("dosimetriPesakit/form_add/$id_pesakit"));
            } else {
                redirect(module_url("dosimetriPesakit/listdospesakit"));
            }
        }

        redirect(module_url("dosimetriPesakit/listdospesakit"));
    }

    // Delete dosimetry record
    public function delete($id_dosimetri)
    {
        try {
            if (empty($id_dosimetri)) {
                throw new Exception('ID rekod tidak sah');
            }

            if (!$this->Dosimetripesakit_model->delete($id_dosimetri)) {
                $error = $this->db->error();
                throw new Exception('Gagal memadam rekod: '.$error['message']);
            }

            $this->session->set_flashdata('success', 'Rekod dosimetri dipadam');
            log_message('info', 'Deleted dosimetry record ID: '.$id_dosimetri);

        } catch (Exception $e) {
            log_message('error', 'Error in DosimetriPesakit/delete: '.$e->getMessage());
            $this->session->set_flashdata('error', $e->getMessage());
        }

        redirect(module_url("dosimetriPesakit/listdospesakit"));
    }

    public function test()
    {
        echo "Controller is working!";
        exit;
    }
}