<?php
class DosimetriPesakit extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("dosimetriPesakit_model");
    }

    // List all patients with dosimetry records
    public function listdospesakit()
    {
        $data['dosimetri'] = $this->dosimetriPesakit_model->get_all_dosimetri();
        
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
        $this->template->render("dosimetri/pilih_pesakit"); // Create this view
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
            redirect(module_url("dosimetri/listdospesakit"));
        }

        $this->template->title("Tambah Rekod Dosimetri");
        $this->template->set("pesakit", $pesakit);
        $this->template->render("dosimetri/form_add_pesakit");
    }

    // Process form submission
    public function add($id_pesakit)
    {
        // Verify patient exists
        $patient = $this->db
            ->where("T01_ID_PESAKIT", $id_pesakit)
            ->get("EV_T01_PESAKIT")
            ->row();

        if (!$patient) {
            $this->session->set_flashdata('error', 'Pesakit tidak dijumpai');
            redirect(module_url("dosimetri/listdospesakit"));
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
            $this->session->set_flashdata('error', validation_errors());
            redirect(module_url("dosimetri/form_add/$id_pesakit"));
        }

        // Prepare data
        $data = [
            "T01_ID_PESAKIT" => $id_pesakit,
            "T03_TUBE_VOLTAGE" => $this->input->post("tube_voltage"),
            "T03_CURRENT_TIME_PRODUCT" => $this->input->post("current_time_product"),
            "T03_EXPOSURE_TIME" => $this->input->post("exposure_time"),
            "T03_SOURCE_IMAGE_DISTANCE" => $this->input->post("source_image_distance"),
            "T03_SOURCE_SKIN_DISTANCE" => $this->input->post("source_skin_distance"),
            "T03_COLLIMATION_FIELD_SIZE" => $this->input->post("collimation_field_size"),
            "T03_GRID" => $this->input->post("grid"),
            "T03_DOSE_AREA_PRODUCT" => $this->input->post("dose_area_product"),
            "T03_EXPOSURE_INDEX" => $this->input->post("exposure_index")
        ];

        // Insert data
        if ($this->dosimetriPesakit_model->create($data)) {
            $this->session->set_flashdata('success', 'Rekod dosimetri berjaya ditambah');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambah rekod dosimetri');
        }

        redirect(module_url("dosimetri/listdospesakit"));
    }

    // Delete dosimetry record
    public function delete($id_dosimetri)
    {
        if ($this->dosimetriPesakit_model->delete($id_dosimetri)) {
            $this->session->set_flashdata('success', 'Rekod dosimetri dipadam');
        } else {
            $this->session->set_flashdata('error', 'Gagal memadam rekod');
        }
        redirect(module_url("dosimetri/listdospesakit"));
    }

    public function test()
{
    echo "Controller is working!";
    exit;
}
}