<?php


class Dokumen_model extends CI_Model
{
    public function get_all_dokumen()
    {
      $query =  $this->db->get("EV_T02_DOKUMEN");
      return $query;
    }


    function delete_dokumen($id_dokumen){
        $this->db
    ->where("T02_ID_DOKUMEN", $id_dokumen)
    ->delete("EV_T02_DOKUMEN");
    }
    
}
