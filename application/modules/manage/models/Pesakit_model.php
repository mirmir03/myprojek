<?php

class Pesakit_model extends CI_Model
{
    public function get_all_pesakit()
    {
        $query = $this->db->get("EV_T01_PESAKIT");
        return $query;
    }

    public function delete_pesakit($id_pesakit) {
        // Check if record has doctor comment
        $patient = $this->db
            ->where("T01_ID_PESAKIT", $id_pesakit)
            ->get("EV_T01_PESAKIT")
            ->row();
    
        if (!empty($patient->T01_DOCTOR_COMMENT)) {
            return false; // Return false if delete not allowed
        }
    
        $this->db->where('T01_ID_PESAKIT', $id_pesakit);
        return $this->db->delete('EV_T01_PESAKIT');
    }

    public function get_all_patients() {
        // You can reuse your existing method
        return $this->get_all_pesakit();
    }

    public function get_patient($id) {
        $this->db->where('T01_ID_PESAKIT', $id);
        $query = $this->db->get('EV_T01_PESAKIT');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    public function get_doctor_comment($patient_id) {
        $this->db->select('T01_DOCTOR_COMMENT');
        $this->db->where('T01_ID_PESAKIT', $patient_id);
        $query = $this->db->get('EV_T01_PESAKIT');
        return $query->row()->T01_DOCTOR_COMMENT ?? null;
    }
    public function update_comment($id, $comment) {
        if (empty($id)) {
            log_message('error', 'Empty ID in update_comment');
            return false;
        }
        
        $this->db->set('T01_DOCTOR_COMMENT', $comment);
        $this->db->where('T01_ID_PESAKIT', $id);
        return $this->db->update('EV_T01_PESAKIT');
    }

    // In pesakit_model.php:
    public function getChartData($startDate = null, $endDate = null, $kategori = null)
    {
        try {
            $this->db->select('T01_BAHAGIAN_UTAMA, T01_KATEGORI, T01_JANTINA, T01_TARIKH');
            $this->db->from('EV_T01_PESAKIT');
    
            if ($startDate && $endDate) {
                $this->db->where("TO_DATE(T01_TARIKH, 'DD-MON-YYYY') >=", date('d-M-Y', strtotime($startDate)));
                $this->db->where("TO_DATE(T01_TARIKH, 'DD-MON-YYYY') <=", date('d-M-Y', strtotime($endDate)));
            }
    
            if ($kategori) {
                $this->db->where('T01_KATEGORI', $kategori);
            }
    
            $query = $this->db->get();
            if (!$query) {
                $error = $this->db->error();
                throw new Exception('Database error: ' . $error['message']);
            }
    
            return $query;
        } catch (Exception $e) {
            log_message('error', 'Model error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function getUniqueValues()
    {
        $result = [];
    
        $this->db->select('DISTINCT(T01_KATEGORI) as value');
        $this->db->from('EV_T01_PESAKIT');
        $this->db->order_by('T01_KATEGORI', 'ASC');
        $query = $this->db->get();
        $result['kategori'] = array_column($query->result_array(), 'value');
    
        return $result;
    }
}
?>

 


