<?php
class Reject extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("reject_model");
        $this->load->library('form_validation');
    }

    public function listreject()
    {
        $data['rejects'] = $this->reject_model->get_all_reject()->result();
        $this->template->title("Senarai Reject");
        $this->template->set("data", $data);
        $this->template->render();
    }

    public function delete($id_reject)
    {
        // Validate CSRF token
        if (!$this->input->post($this->security->get_csrf_token_name())) {
            $this->session->set_flashdata('error', 'Invalid CSRF token');
            redirect(module_url("reject/listreject"));
        }

        if (!$id_reject) {
            $this->session->set_flashdata('error', 'Invalid reject ID');
            redirect(module_url("reject/listreject"));
        }

        $result = $this->reject_model->delete_reject($id_reject);
        
        $this->session->set_flashdata(
            $result ? 'success' : 'error',
            $result ? 'Reject deleted successfully.' : 'Failed to delete reject.'
        );
        
        redirect(module_url("reject/listreject"));
    }

    public function add()
    {
        // Set validation rules
        $this->form_validation->set_rules('jenis_reject', 'Jenis Reject', 'required');
        $this->form_validation->set_rules('tarikh', 'Tarikh', 'required|valid_date');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(module_url("reject/form_add"));
        }

        // Get current staff ID (from session or dummy value)
        $staff_id = $this->session->userdata('staff_id') ?? 'DUMMY_STAFF';

        $data_to_insert = [
            "T02_ID_STAF" => $staff_id, // Added staff reference
            "T06_JENIS_REJECT" => $this->input->post("jenis_reject"),
            'T06_TARIKH' => date('Y-m-d H:i:s', strtotime($this->input->post("tarikh")))
        ];
        
        $inserted = $this->reject_model->insert_reject($data_to_insert);

        $this->session->set_flashdata(
            $inserted ? 'success' : 'error',
            $inserted ? 'Reject added successfully.' : 'Failed to add reject.'
        );
        
        redirect(module_url("reject/listreject"));
    }

    public function form_add()
    {
        $this->template->render();
    }

    public function form_edit($id_reject)
    {
        $reject = $this->reject_model->get_reject($id_reject);

        if (!$reject) {
            $this->session->set_flashdata('error', 'Reject not found.');
            redirect(module_url("reject/listreject"));
        }

        $this->template->set("reject", $reject);
        $this->template->render();
    }

    public function save($id_reject)
    {
        // Set validation rules
        $this->form_validation->set_rules('jenis_reject', 'Jenis Reject', 'required');
        $this->form_validation->set_rules('tarikh', 'Tarikh', 'required|valid_date');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(module_url("reject/form_edit/$id_reject"));
        }

        $data_to_update = [
            "T06_JENIS_REJECT" => $this->input->post("jenis_reject"),
            'T06_TARIKH' => date('Y-m-d H:i:s', strtotime($this->input->post("tarikh")))
        ];

        $updated = $this->reject_model->update_reject($id_reject, $data_to_update);

        $this->session->set_flashdata(
            $updated ? 'success' : 'error',
            $updated ? 'Reject updated successfully.' : 'Failed to update reject.'
        );
            
        redirect(module_url("reject/listreject"));
    }
}