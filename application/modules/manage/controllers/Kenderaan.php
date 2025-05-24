<?php 

class Kenderaan extends Admin_Controller 
{

    public function __construct() //adalah utk mewakili skali sahaja dlm overall bagi coding($this->load->model("vehicle_model");)
    {
        parent::__construct();
    $this->load->model("vehicle_model");
    }

    public function listkend()
    {
        $this->load->model("vehicle_model");//utk panggil model vehicle dlm vehicle_model 

        //$data = $this->db->get("EV_T01_KENDERAAN");

        $data = $this->vehicle_model->get_all_kenderaan();

        $this->template->title("Senarai kenderaan");
        $this->template->set("data", $data);//hantar variable data ke view
        $this->template->render();

    }

    public function delete($id_kenderaan, $id2="")
     {
    //$id... merujuk kepada parameter which is 1 parameter
    // in url(manage/kenderaan/listkend/1)

    $this->load->model("vehicle_model");
    $this->vehicle_model->delete_vehicle($id_kenderaan);
    
    redirect (module_url("kenderaan/listkend"));
 }

 public function add()
 {
   $no_plat = $this->input->post("no_plat");//nama dlm () mesti sama dgn dlm name in form_add() views
   $nama_kend = $this->input->post("nama_kend");
   $var = $this->input->post("var");

   $data_to_insert = [
    "T01_FLAT" => $no_plat,
    "T01_NAMA_KENDERAAN" => $nama_kend,
    "T01_VARIAN" => $var,
];
 
$this->db->insert("EV_T01_KENDERAAN", $data_to_insert);

redirect (module_url("kenderaan/listkend"));

 }

 public function form_add()
 {
    $this->template->render();
    //echo "123"; 
 }

 public function form_edit($id_kenderaan)//form edit mesti kena ada terima parameter
 {
    $vehicle = $this->db
    ->where("T01_ID_KENDERAAN", $id_kenderaan)
    ->get("EV_T01_KENDERAAN")
    ->row();

    
    $this->template->set("vehicle", $vehicle);
    $this->template->render();
    //echo "123";
   
 }

 public function save($id_kenderaan)
 {
   $no_plat = $this->input->post("no_plat");//nama dlm () mesti sama dgn dlm name in form_add() views
   $nama_kend = $this->input->post("nama_kend");
   $var = $this->input->post("var");

   $data_to_update = [
    "T01_FLAT" => $no_plat,
    "T01_NAMA_KENDERAAN" => $nama_kend,
    "T01_VARIAN" => $var,
];
 
$this->db
->where("T01_ID_KENDERAAN", $id_kenderaan )
->update("EV_T01_KENDERAAN", $data_to_update);

redirect (module_url("kenderaan/listkend"));

 }


}
?>