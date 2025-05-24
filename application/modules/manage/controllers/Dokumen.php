<?php


class Dokumen extends Admin_Controller
{


    public function __construct() //adalah utk mewakili skali sahaja dlm overall bagi coding($this->load->model("vehicle_model");)
    {
        parent::__construct();
    $this->load->model("dokumen_model");
    }


    public function listdokumen()
    {


        //$data = $this->db->get("EV_T01_DOKUMEN");


        $data = $this->dokumen_model->get_all_dokumen();


        $this->template->title("Senarai dokumen");
        $this->template->set("data", $data);//hantar variable data ke view
        $this->template->render();


    }


    public function delete($id_dokumen, $id2="")
     {
    //$id... merujuk kepada parameter which is 1 parameter
    // in url(manage/kenderaan/listkend/1)


    $this->dokumen_model->delete_dokumen($id_dokumen);
   
    redirect (module_url("dokumen/listdokumen"));
 }


 public function add()
{
    $this->load->library('upload');

    // Set the upload configuration
    $config['upload_path'] = FCPATH . 'www-uploads/';
    $config['allowed_types'] = 'jpg|png|pdf|docx';
    $config['max_size'] = 10000;
    $config['max_height'] = 34325354;
    $config['encrypt_name'] = FALSE;

    $this->upload->initialize($config);

    // Process 'tarikh' (date) from the form input
    $tarikh = $this->input->post("tarikh");
    if ($tarikh) {
        try {
            $tarikh = new DateTime($tarikh);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Invalid date format.');
            redirect(module_url("dokumen/form_add"));
        }
    } else {
        $this->session->set_flashdata('error', 'Date is required.');
        redirect(module_url("dokumen/form_add"));
    }

    // Initialize data array with the date
    $data_to_insert = [
        'T02_TARIKH' => $tarikh->format('d-M-Y')
    ];

    // Define the file fields to process
    $file_fields = [
        'dokumen_reject' => 'T02_DOKUMEN_REJECT_ANALYSIS',
        'dokumen_certificate' => 'T02_DOKUMEN_CME_CERTIFICATION',
        'dokumen_audit' => 'T02_AUDIT_IMAGE',
        'dokumen_qc' => 'T02_DOKUMEN_LAPORANQC'
    ];

    // Process each file field
    foreach ($file_fields as $field => $column) {
        if (!empty($_FILES[$field]['name'])) {
            // Attempt to upload the file
            if (!$this->upload->do_upload($field)) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(module_url("dokumen/form_add"));
            } else {
                // Get uploaded file data
                $uploaded_data = $this->upload->data();
                $original_filename = str_replace('_', ' ', $uploaded_data['orig_name']);

                // Save the file path
                $data_to_insert[$column] = $original_filename;

                // Rename the file to restore spaces
                rename(
                    $uploaded_data['full_path'],
                    $uploaded_data['file_path'] . $original_filename
                );
            }
        }
        // If no file uploaded for this field, it will not be included in data_to_insert
    }

    // Check if at least one file was uploaded
    if (count($data_to_insert) <= 1) { // If only date is present
        $this->session->set_flashdata('error', 'Please upload at least one document.');
        redirect(module_url("dokumen/form_add"));
    }

    // Insert data into database
    $this->db->insert("EV_T02_DOKUMEN", $data_to_insert);

    $this->session->set_flashdata('success', 'Document(s) uploaded successfully.');
    redirect(module_url("dokumen/listdokumen"));
}
 

 public function form_add()
 {
    $this->template->render();
    //echo "123";
 }


 public function form_edit($id_dokumen)
 {
     $dokumen = $this->db
         ->where("T02_ID_DOKUMEN", $id_dokumen)
         ->get("EV_T02_DOKUMEN")
         ->row();
 
     if (!$dokumen) {
         $this->session->set_flashdata('error', 'Document not found.');
         redirect(module_url("dokumen/listdokumen"));
     }
 
     $this->template->set("dokumen", $dokumen);
     $this->template->render();
 }
 

 public function save($id_dokumen)
{
    $this->load->library('upload');

    // Set the upload configuration
    $config['upload_path'] = FCPATH . 'www-uploads/';
    $config['allowed_types'] = 'jpg|png|pdf|docx';
    $config['max_size'] = 10000;
    $config['encrypt_name'] = FALSE;

    $this->upload->initialize($config);

    // Process 'tarikh' (date) from the form input
    $tarikh = $this->input->post("tarikh");
    if ($tarikh) {
        try {
            $tarikh = new DateTime($tarikh);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'Invalid date format.');
            redirect(module_url("dokumen/form_edit/$id_dokumen"));
        }
    } else {
        $this->session->set_flashdata('error', 'Date is required.');
        redirect(module_url("dokumen/form_edit/$id_dokumen"));
    }

    // Initialize the update array with the date
    $data_to_update = [
        'T02_TARIKH' => $tarikh->format('d-M-Y')
    ];

    // Define the file fields to process
    $file_fields = [
        'dokumen_reject' => 'T02_DOKUMEN_REJECT_ANALYSIS',
        'dokumen_certificate' => 'T02_DOKUMEN_CME_CERTIFICATION',
        'dokumen_audit' => 'T02_AUDIT_IMAGE',
        'dokumen_qc' => 'T02_DOKUMEN_LAPORANQC'
    ];

    // Process each file field
    foreach ($file_fields as $field => $column) {
        if (!empty($_FILES[$field]['name'])) {
            // Attempt to upload the file
            if (!$this->upload->do_upload($field)) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect(module_url("dokumen/form_edit/$id_dokumen"));
            } else {
                // Get uploaded file data
                $uploaded_data = $this->upload->data();
                $original_filename = str_replace('_', ' ', $uploaded_data['orig_name']);

                // Save the file path to the database
                $data_to_update[$column] = $original_filename;

                // Rename the file to restore spaces
                rename(
                    $uploaded_data['full_path'],
                    $uploaded_data['file_path'] . $original_filename
                );
            }
        }
    }

    // Update the database with the new data
    $this->db
        ->where("T02_ID_DOKUMEN", $id_dokumen)
        ->update("EV_T02_DOKUMEN", $data_to_update);

    $this->session->set_flashdata('success', 'Document updated successfully.');
    redirect(module_url("dokumen/listdokumen"));
}
}