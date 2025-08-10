<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PdfController extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("pesakit_model");
    }

    public function generate_patient_graph_pdf()
    {
        $bahagian_utama = $this->input->post('bahagian_utama');
        $kategori = $this->input->post('kategori');
        $month = $this->input->post('month');
        $year = $this->input->post('year');

        if (empty($bahagian_utama) || empty($kategori)) {
            show_error('Please select both Bahagian Utama and Kategori');
        }

        $chart_data = $this->pesakit_model->get_graph_data($bahagian_utama, $kategori, $month, $year);

        $data = [
            'bahagian_utama' => $bahagian_utama,
            'kategori' => $kategori,
            'month' => $month,
            'year' => $year,
            'chart_data' => $chart_data,
            'generated_date' => date('d/m/Y H:i')
        ];

        // Load view HTML
        $html = $this->load->view('pesakit/patient_graph_pdf', $data, true);

        // Load mPDF manually
        require_once APPPATH . '../third_party/mpdf/mpdf.php';

        $mpdf = new mPDF('', 'A4-L'); // landscape format
        $mpdf->WriteHTML($html);
        $mpdf->Output('patient_statistics_' . date('Ymd_His') . '.pdf', 'D'); // Download
    }
}
