<?php
class Reject_model extends CI_Model
{
    public function get_all_reject()
    {
        $query = $this->db->get("EV_T06_REJECT_ANALYSIS");
        return $query;
    }

    public function delete_reject($id_reject) {
        $this->db->where('T06_ID_REJECT', $id_reject);
        return $this->db->delete('EV_T06_REJECT_ANALYSIS');
    }

    public function get_reject($id) {
        $this->db->where('T06_ID_REJECT', $id);
        $query = $this->db->get('EV_T06_REJECT_ANALYSIS');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

     public function insert_reject($data) {
        return $this->db->insert("EV_T06_REJECT_ANALYSIS", $data);
    }

    public function update_reject($id, $data) {
        $this->db->where("T06_ID_REJECT", $id);
        return $this->db->update("EV_T06_REJECT_ANALYSIS", $data);
    }
}