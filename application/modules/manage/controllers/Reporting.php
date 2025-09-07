<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pesakit_model');
    }

    public function index()
    {
        // 1. Total active patients by Bahagian Utama
        $active_patients_by_bahagian = $this->pesakit_model->get_active_patients_by_bahagian();

        // 2. Patient count by gender and sub bahagian
        $patient_by_gender_sub = $this->pesakit_model->get_patient_count_by_gender_subbahagian();

        // 3. Active patients by month chart data
        $active_patients_by_month = $this->pesakit_model->get_active_pesakit_by_month();

        // 4. Active patients by Bahagian Utama over months
        $patients_by_bahagian_month = $this->pesakit_model->get_all_bahagian_utama_by_month();

        // 5. Patient category distribution
        $category_distribution = $this->pesakit_model->get_patient_category_distribution();

        // 6. Patients with doctor comments
        $patients_with_comments = $this->pesakit_model->get_patients_with_comments();

        // Pass all data to view
        $this->template->set('active_patients_by_bahagian', $active_patients_by_bahagian);
        $this->template->set('patient_by_gender_sub', $patient_by_gender_sub);
        $this->template->set('active_patients_by_month', json_encode($active_patients_by_month));
        $this->template->set('patients_by_bahagian_month', json_encode($patients_by_bahagian_month));
        $this->template->set('category_distribution', $category_distribution);
        $this->template->set('patients_with_comments', $patients_with_comments);

        $this->template->title('Pesakit Reporting Dashboard');
        $this->template->set_breadcrumb('Reporting');
        
        $this->template->render('reporting/reporting_view');
    }
}
