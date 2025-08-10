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
    // Show patient selection page (updated version)
public function form_add_pesakit()
{
    log_message('debug', 'form_add_pesakit() was called');

    // Load patients who DON'T have dosimetry records yet, ordered by date descending
    $this->db->select('EV_T01_PESAKIT.*');
    $this->db->from('EV_T01_PESAKIT');
    $this->db->join('EV_T03_DOSIMETRI_PESAKIT', 'EV_T01_PESAKIT.T01_ID_PESAKIT = EV_T03_DOSIMETRI_PESAKIT.T01_ID_PESAKIT', 'left');
    $this->db->where('EV_T03_DOSIMETRI_PESAKIT.T01_ID_PESAKIT IS NULL');
    
    // Sort by date field (use your actual date column name)
    $this->db->order_by("EV_T01_PESAKIT.T01_TARIKH", "DESC"); // Change T01_TARIKH to your actual date column
    
    $data['pesakit'] = $this->db->get()->result();
    
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
        $config['upload_path'] = './uploads/pdf/';
$config['allowed_types'] = 'pdf';
$config['max_size'] = 5120; // 5MB
$config['encrypt_name'] = TRUE;

$this->load->library('upload', $config);

$pdf_file_name = null;

if (!empty($_FILES['pdf_file']['name'])) {
    if (!$this->upload->do_upload('pdf_file')) {
        $error = $this->upload->display_errors();
        $this->session->set_flashdata('error', 'Gagal muat naik fail PDF: ' . $error);
        redirect(current_url()); // Or go back to the form
    } else {
        $upload_data = $this->upload->data();
        $pdf_file_name = $upload_data['file_name'];
    }
}

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
$this->form_validation->set_rules('tube_voltage', 'Voltan Tiub', 'required|numeric|greater_than_equal_to[40]|less_than_equal_to[150]');
$this->form_validation->set_rules('current_time_product', 'Arus-Masa', 'required|numeric|greater_than_equal_to[0.5]|less_than_equal_to[600]');
$this->form_validation->set_rules('exposure_time', 'Masa Pendedahan', 'required|numeric|greater_than_equal_to[500]|less_than_equal_to[4000]');
$this->form_validation->set_rules('source_image_distance', 'Jarak Sumber-Gambar', 'required|numeric|greater_than_equal_to[90]|less_than_equal_to[200]');
$this->form_validation->set_rules('source_skin_distance', 'Jarak Sumber-Kulit', 'required|numeric|greater_than_equal_to[30]|less_than_equal_to[100]');

            $this->form_validation->set_rules('collimation_field_size', 'Saiz Medan Kolimasi', 'required');
            $this->form_validation->set_rules('grid', 'Grid', 'required|in_list[Ya,Tidak]');
            $this->form_validation->set_rules('dose_area_product', 'DAP', 'numeric');
            $this->form_validation->set_rules('exposure_index', 'Indeks Pendedahan', 'numeric|greater_than_equal_to[100]|less_than_equal_to[9000]');

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

    // Show edit form for a dosimetry record
public function form_edit_pesakit($id_dosimetri)
{
    // Get the dosimetry record
    $record = $this->db
        ->where('T03_ID_DOS_PESAKIT', $id_dosimetri)
        ->get('EV_T03_DOSIMETRI_PESAKIT')
        ->row();

    if (!$record) {
        $this->session->set_flashdata('error', 'Rekod dosimetri tidak dijumpai.');
        redirect(module_url('dosimetriPesakit/listdospesakit'));
        return;
    }

    // Get the patient info
    $pesakit = $this->db
        ->where('T01_ID_PESAKIT', $record->T01_ID_PESAKIT)
        ->get('EV_T01_PESAKIT')
        ->row();

    if (!$pesakit) {
        $this->session->set_flashdata('error', 'Rekod pesakit tidak dijumpai.');
        redirect(module_url('dosimetriPesakit/listdospesakit'));
        return;
    }

    $this->template->title('Kemaskini Rekod Dosimetri');
    $this->template->set('record', $record);
    $this->template->set('pesakit', $pesakit);
    $this->template->render('dosimetri/form_edit_pesakit');
}


// Process update submission
public function update($id_dosimetri = null)
{
     if (!$id_dosimetri) {
        show_error('Invalid or missing dosimetry record ID.');
    }

    try {
        $record = $this->db->where('T03_ID_DOS_PESAKIT', $id_dosimetri)->get('EV_T03_DOSIMETRI_PESAKIT')->row();
        if (!$record) throw new Exception("Rekod dosimetri tidak dijumpai");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('tube_voltage', 'Voltan Tiub', 'required|numeric|greater_than_equal_to[40]|less_than_equal_to[150]');
        $this->form_validation->set_rules('current_time_product', 'Arus-Masa', 'required|numeric|greater_than_equal_to[0.5]|less_than_equal_to[600]');
        $this->form_validation->set_rules('exposure_time', 'Masa Pendedahan', 'required|numeric|greater_than_equal_to[500]|less_than_equal_to[4000]');
        $this->form_validation->set_rules('source_image_distance', 'Jarak Sumber-Gambar', 'required|numeric|greater_than_equal_to[90]|less_than_equal_to[200]');
        $this->form_validation->set_rules('source_skin_distance', 'Jarak Sumber-Kulit', 'required|numeric|greater_than_equal_to[30]|less_than_equal_to[100]');
        $this->form_validation->set_rules('collimation_field_size', 'Saiz Medan Kolimasi', 'required');
        $this->form_validation->set_rules('grid', 'Grid', 'required|in_list[Ya,Tidak]');
        $this->form_validation->set_rules('dose_area_product', 'DAP', 'numeric');
        $this->form_validation->set_rules('exposure_index', 'Indeks Pendedahan', 'numeric|greater_than_equal_to[100]|less_than_equal_to[9000]');

        if ($this->form_validation->run() == FALSE) {
            throw new Exception(validation_errors());
        }

        $data = [
            "T03_TUBE_VOLTAGE" => (float)$this->input->post("tube_voltage"),
            "T03_CURRENT_TIME_PRODUCT" => (float)$this->input->post("current_time_product"),
            "T03_EXPOSURE_TIME" => (float)$this->input->post("exposure_time"),
            "T03_SOURCE_IMAGE_DISTANCE" => (float)$this->input->post("source_image_distance"),
            "T03_SOURCE_SKIN_DISTANCE" => (float)$this->input->post("source_skin_distance"),
            "T03_COLLIMATION_FIELD_SIZE" => $this->input->post("collimation_field_size"),
            "T03_GRID" => $this->input->post("grid"),
        ];

        // Only update optional fields if present
        if ($this->input->post("dose_area_product") !== '') {
            $data["T03_DOSE_AREA_PRODUCT"] = (float)$this->input->post("dose_area_product");
        }

        if ($this->input->post("exposure_index") !== '') {
            $data["T03_EXPOSURE_INDEX"] = (float)$this->input->post("exposure_index");
        }

        $this->db->where('T03_ID_DOS_PESAKIT', $id_dosimetri);
        $this->db->update('EV_T03_DOSIMETRI_PESAKIT', $data);

        $this->session->set_flashdata('success', 'Rekod dosimetri berjaya dikemaskini');
    } catch (Exception $e) {
        log_message('error', 'Error in update: '.$e->getMessage());
        $this->session->set_flashdata('error', $e->getMessage());
        redirect(module_url("dosimetriPesakit/form_edit/".$id_dosimetri));
    }

    redirect(module_url("dosimetriPesakit/listdospesakit"));
}

    public function test()
    {
        echo "Controller is working!";
        exit;
    }

    public function get_dosimetri($id) {
    $this->db->where('T04_ID_DOS_STAF', $id);
    $query = $this->db->get('EV_T04_DOSIMETRI_STAFF');
    
    if ($query->num_rows() > 0) {
        $row = $query->row();
        
        // Convert back to decimals
        $row->T04_DOS_SETARA1 = $this->_toDecimal($row->T04_DOS_SETARA1);
        $row->T04_DOS_SETARA2 = $this->_toDecimal($row->T04_DOS_SETARA2);
        $row->T04_DOS_AVE1 = $this->_toDecimal($row->T04_DOS_AVE1);
        $row->T04_DOS_AVE2 = $this->_toDecimal($row->T04_DOS_AVE2);
        
        // Handle Oracle date format
        if (!empty($row->T04_TARIKH)) {
            if (is_object($row->T04_TARIKH)) {
                // If it's a DateTime object (OCI8)
                $row->T04_TARIKH = $row->T04_TARIKH->format('Y-m-d');
            } else {
                // If it's a string
                try {
                    $date = new DateTime($row->T04_TARIKH);
                    $row->T04_TARIKH = $date->format('Y-m-d');
                } catch (Exception $e) {
                    // Try Oracle date format
                    $date = date_create_from_format('d-M-Y', $row->T04_TARIKH);
                    if ($date) {
                        $row->T04_TARIKH = $date->format('Y-m-d');
                    } else {
                        $row->T04_TARIKH = date('Y-m-d'); // fallback to today
                    }
                }
            }
        }
        
        return $row;
    }
    return false;
}

public function export_dosimetri_excel()
{
    // Get dosimetri data with patient details
    $this->db->select('d.*, p.T01_NO_RUJUKAN, p.T01_NAMA_PESAKIT, p.T01_TARIKH');
    $this->db->from('EV_T03_DOSIMETRI_PESAKIT d');
    $this->db->join('EV_T01_PESAKIT p', 'p.T01_ID_PESAKIT = d.T01_ID_PESAKIT', 'left');
    $this->db->order_by('d.T03_ID_DOS_PESAKIT', 'DESC');

    $result = $this->db->get()->result();

    // Set CSV headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="Senarai_Dosimetri_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // ✅ Add "Tarikh" to header
    fputcsv($output, [
        'No', 'No Pengenalan', 'Tarikh', 'Voltan Tiub', 'Arus-Masa', 'Masa Pendedahan',
        'Jarak Sumber-Gambar', 'Jarak Sumber-Kulit', 'Saiz Medan Kolimasi', 'Grid',
        'DAP', 'Indeks Pendedahan'
    ]);

    // Data rows
    $no = 1;
    foreach ($result as $row) {
        fputcsv($output, [
            $no++,
            $row->T01_NO_RUJUKAN ?? 'N/A',
            isset($row->T01_TARIKH) ? date('Y-m-d', strtotime($row->T01_TARIKH)) : 'N/A',
            $row->T03_TUBE_VOLTAGE,
            $row->T03_CURRENT_TIME_PRODUCT,
            $row->T03_EXPOSURE_TIME,
            $row->T03_SOURCE_IMAGE_DISTANCE,
            $row->T03_SOURCE_SKIN_DISTANCE,
            $row->T03_COLLIMATION_FIELD_SIZE,
            $row->T03_GRID,
            $row->T03_DOSE_AREA_PRODUCT,
            $row->T03_EXPOSURE_INDEX
        ]);
    }

    fclose($output);
    exit;
}

    
}