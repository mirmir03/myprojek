<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient_Reporting extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pesakit_model');
        // Load any models if needed for database queries
        // $this->load->model('Patient_model');
    }

    public function index()
    {
        echo "Controller loaded!";
        // Example static data (replace with model queries)
        $data = [
            'new_patients'    => 890,
            'opd_patients'    => 360,
            'lab_tests'       => 980,
            'total_earnings'  => 98000,
            'growth_new'      => 40,
            'growth_opd'      => 30,
            'growth_lab'      => 60,
            'growth_earnings' => 20
        ];

        // Load view in admin layout
        $this->template->build('patient_reporting', $data);
    }
}
