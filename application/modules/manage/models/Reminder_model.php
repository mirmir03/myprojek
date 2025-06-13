<?php

class Reminder_model extends CI_Model {
    
    public function getAllReminders()
    {
        $query = $this->db->get("EV_T07_NOTIFIKASI_PESANAN");
        return $query;
    }

    public function deleteReminder($reminder_id) 
    {
        // Check if reminder has been processed
        $reminder = $this->db
            ->where("T06_ID_NOTIFIKASI", $reminder_id)
            ->get("EV_T07_NOTIFIKASI_PESANAN")
            ->row();
            
        if (!empty($reminder->T06_PROSES_TARIKH)) {
            return false; // Return false if delete not allowed
        }
        
        $this->db->where('T06_ID_NOTIFIKASI', $reminder_id);
        return $this->db->delete('EV_T07_NOTIFIKASI_PESANAN');
    }

    public function getReminder($reminder_id) 
    {
        $this->db->where('T06_ID_NOTIFIKASI', $reminder_id);
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    public function getProcessedDate($reminder_id) 
    {
        $this->db->select('T06_PROSES_TARIKH');
        $this->db->where('T06_ID_NOTIFIKASI', $reminder_id);
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        return $query->row()->T06_PROSES_TARIKH ?? null;
    }

    public function updateProcessedDate($reminder_id, $date) 
    {
        if (empty($reminder_id)) {
            log_message('error', 'Empty ID in updateProcessedDate');
            return false;
        }
        
        $this->db->set('T06_PROSES_TARIKH', $date);
        $this->db->where('T06_ID_NOTIFIKASI', $reminder_id);
        return $this->db->update('EV_T07_NOTIFIKASI_PESANAN');
    }

    public function getChartData($startDate = null, $endDate = null, $supplier = null)
    {
        try {
            $this->db->select('T06_NAMA_PEMBEKAL, T06_TARIKH_PESANAN, T06_TARIKH_TAMAT, T06_JUMLAH_HARGA');
            $this->db->from('EV_T07_NOTIFIKASI_PESANAN');
            
            if ($startDate && $endDate) {
                $this->db->where("TO_DATE(T06_TARIKH_PESANAN, 'DD-MON-YYYY') >=", date('d-M-Y', strtotime($startDate)));
                $this->db->where("TO_DATE(T06_TARIKH_PESANAN, 'DD-MON-YYYY') <=", date('d-M-Y', strtotime($endDate)));
            }
            
            if ($supplier) {
                $this->db->where('T06_NAMA_PEMBEKAL', $supplier);
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
        
        // Get unique suppliers
        $this->db->select('DISTINCT(T06_NAMA_PEMBEKAL) as value');
        $this->db->from('EV_T07_NOTIFIKASI_PESANAN');
        $this->db->order_by('T06_NAMA_PEMBEKAL', 'ASC');
        $query = $this->db->get();
        $result['suppliers'] = array_column($query->result_array(), 'value');
        
        return $result;
    }

    public function addReminder($data)
    {
        return $this->db->insert('EV_T07_NOTIFIKASI_PESANAN', $data);
    }

    public function updateReminder($reminder_id, $data)
    {
        $this->db->where('T06_ID_NOTIFIKASI', $reminder_id);
        return $this->db->update('EV_T07_NOTIFIKASI_PESANAN', $data);
    }
}