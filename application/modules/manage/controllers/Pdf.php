<?php

class Pdf extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pdfreader');
    }

    public function index()
    {
        $this->template->title("Upload & Extract PDF");
        $this->template->render(); // will load upload_pdf_form.php by default
    }

    public function upload()
    {
        $this->load->library('upload');

        $config['upload_path'] = FCPATH . 'www-uploads/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 5000;
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('pdf_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect(module_url('pdf'));
        }

        $uploaded_data = $this->upload->data();
        $full_path = $uploaded_data['full_path'];

        // Extract data from PDF using Pdfreader library
        try {
            $extracted = $this->pdfreader->extractInfo($full_path);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'PDF parsing failed: ' . $e->getMessage());
            redirect(module_url('pdf'));
        }

        // Send extracted data to view
        $this->template->title("PDF Extraction Result");
        $this->template->set("info", $extracted);
        $this->template->render('upload_pdf_form'); // reuse form view, but display result
    }
}
