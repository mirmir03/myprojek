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
        // Prepare any data for the graphs if needed
       $this->template->set('patient_graph', $this->load->view('pesakit/patient_graph', [], TRUE));
$this->template->set('graph', $this->load->view('reject/graph', [], TRUE));
//$this->template->set('graph_po', $this->load->view('po/po_graph', [], TRUE));
//$this->template->set('graph_dosimetry', $this->load->view('dosimetry/dosimetry_graph', [], TRUE));
 // Set page title + breadcrumb
        $this->template->title("Dashboard");
        $this->template->set_breadcrumb("Dashboard");

        // Render the dashboard view
        $this->template->render();


    }
}
