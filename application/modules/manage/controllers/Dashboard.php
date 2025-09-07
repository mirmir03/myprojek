<?php
class Dashboard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        // load any models needed for your graphs
        $this->load->model('pesakit_model');
        $this->load->model('reject_model');
        $this->load->model('notifikasi_model');
        $this->load->model('dosimetristaf_model');
    }

    public function index()
{
    // Active patients
    $total_active_patients = $this->pesakit_model->get_active_pesakit_count();
    $this->template->set('total_active_patients', $total_active_patients);

    // Rejects - Get ALL rejects for the card
    $total_rejects_count = $this->reject_model->get_total_reject_all();
    $this->template->set('total_rejects_count', $total_rejects_count);

    // Notifikasi
    $total_unreceived_notifikasi = $this->notifikasi_model->get_unreceived_count();
    $this->template->set('total_unreceived_notifikasi', $total_unreceived_notifikasi);

    // Active patients by month
    $active_patients_chart = $this->pesakit_model->get_active_pesakit_by_month();
    foreach ($active_patients_chart['datasets'] as &$dataset) {
        $dataset['data'] = array_map('intval', $dataset['data']);
    }
    $this->template->set('active_patients_chart', json_encode($active_patients_chart));

     // Bahagian Utama by month (single bahagian)
$current_year = date('Y');
$all_bahagian_chart = $this->pesakit_model->get_all_bahagian_utama_by_month($current_year);
$this->template->set('all_bahagian_chart', json_encode($all_bahagian_chart));
$this->template->set('current_year', $current_year);

// **Remove this** because you don't need single bahagian_utama chart:
# $this->template->set('bahagian_utama_chart', json_encode($bahagian_utama_chart));



    // Reject Analysis Pie Chart - Filtered by current year only
    $current_year = date('Y');
    $reject_chart_raw = $this->reject_model->get_reject_chart_data(null, $current_year);

    // If empty, fallback to all data
    if (empty($reject_chart_raw)) {
        $reject_chart_raw = $this->reject_model->get_reject_chart_data();
    }

    $reject_labels = [];
    $reject_counts = [];
    $reject_colors = [/*...colors...*/];

    foreach ($reject_chart_raw as $i => $row) {
        if ($i >= 12) break;
        $reject_labels[] = $row->T06_JENIS_REJECT;
        $reject_counts[] = (int)$row->TOTAL_REJECTS;
    }

    $reject_chart_data = [
        'labels' => $reject_labels,
        'datasets' => [[
            'data' => $reject_counts,
            'backgroundColor' => array_slice($reject_colors, 0, count($reject_counts))
        ]]
    ];

    $this->template->set('reject_chart_data', json_encode($reject_chart_data));
    $this->template->set('current_year_rejects', array_sum($reject_counts));

    // Render
    $this->template->title("Dashboard");
    $this->template->set_breadcrumb("Dashboard");
    $this->template->render();
}


    public function get_active_patient_count()
{
    $count = $this->pesakit_model->get_active_pesakit_count();
    echo json_encode(['count' => $count]);
}

public function get_total_rejects_count()
{
    $count = $this->reject_model->get_total_reject_all();
    echo json_encode(['count' => $count]);
}

public function get_total_unreceived_notifikasi()
{
    $count = $this->notifikasi_model->get_unreceived_count();
    echo json_encode(['count' => $count]);
}

}