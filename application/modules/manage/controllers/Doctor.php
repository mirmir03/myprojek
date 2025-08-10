<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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

public function delete_comment() {
    // Log that the method was called
    log_message('debug', 'delete_comment method called');
    
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        log_message('error', 'delete_comment: Not a POST request. Method: ' . $_SERVER['REQUEST_METHOD']);
        $this->output
            ->set_status_header(405)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Method not allowed'
            ]));
        return;
    }
    
    // Get all POST data for debugging
    $post_data = $this->input->post();
    log_message('debug', 'delete_comment POST data: ' . json_encode($post_data));
    
    $patient_id = $this->input->post('patient_id');
    log_message('debug', 'delete_comment patient_id: ' . $patient_id);
    
    // Validate patient_id
    if (empty($patient_id)) {
        log_message('error', 'delete_comment: Empty patient_id');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Patient ID is required'
            ]));
        return;
    }
    
    // Check if patient exists
    $patient = $this->pesakit_model->get_patient($patient_id);
    if (!$patient) {
        log_message('error', 'delete_comment: Patient not found with ID: ' . $patient_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Patient not found'
            ]));
        return;
    }
    
    log_message('debug', 'delete_comment: Patient found, proceeding with deletion');
    
    try {
        // Call model method
        $result = $this->pesakit_model->delete_doctor_comment($patient_id);
        log_message('debug', 'delete_comment: Model result: ' . ($result ? 'true' : 'false'));
        
        // Return JSON response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Comment deleted successfully' : 'Failed to delete comment',
                'csrf_token' => $this->security->get_csrf_hash()
            ]));
            
    } catch (Exception $e) {
        log_message('error', 'delete_comment exception: ' . $e->getMessage());
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]));
    }
}
// Add this method to your Doctor controller for testing
// File: application/modules/manage/controllers/Doctor.php

public function test_delete_url() {
    echo "Test URL is working!<br>";
    echo "Controller: " . $this->router->class . "<br>";
    echo "Method: " . $this->router->method . "<br>";
    echo "Base URL: " . base_url() . "<br>";
    
    // Test the actual delete_comment method
    echo "<br>Testing delete_comment method...<br>";
    
    // Simulate POST data for testing
    $_POST['patient_id'] = '1';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    $this->delete_comment();
}
}