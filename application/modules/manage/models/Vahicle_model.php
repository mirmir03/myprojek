<?php

class Vehicle_model extends CI_Model
{
    public function get_all_kenderaan()
    {
      $query =  $this->db->get("EV_T01_KENDERAAN");
      return $query;
    }

    function delete_vehicle($id_kenderaan){
        $this->db
    ->where("T01_ID_KENDERAAN", $id_kenderaan)
    ->delete("EV_T01_KENDERAAN");
    }
}