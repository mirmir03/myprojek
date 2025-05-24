<?php
class Doctor extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        log_message('debug', 'Doctor controller loaded');
        $this->load->model("pesakit_model");
    }

    public function index()
    {
        $data['data'] = $this->pesakit_model->get_all_patients();
        $this->template->title("Patient List");
        $this->template->set($data);
        $this->template->render();
    }

    public function view_patient($id_pesakit)
    {
        $data['patient'] = $this->pesakit_model->get_patient($id_pesakit);
        
        if (!$data['patient']) {
            show_404();
        }
        
        $this->template->title("Patient Details");
        $this->template->set($data);
        $this->template->render();
    }

    public function add_comment() {
        if (!$this->input->post()) {
            show_error('Invalid request', 400);
        }
    
        $patient_id = $this->input->post('patient_id');
        $comment = $this->input->post('doctor_comment');
        
        if (empty($patient_id)) {
            log_message('error', 'Empty patient_id in add_comment');
            show_error('Patient ID is required', 400);
        }
        
        $result = $this->pesakit_model->update_comment($patient_id, $comment);
        
        if ($result) {
            $this->session->set_flashdata('success', 'Comment saved successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to save comment');
        }
        
        redirect(site_url('manage/doctor/view_patient/'.$patient_id));
    }
}