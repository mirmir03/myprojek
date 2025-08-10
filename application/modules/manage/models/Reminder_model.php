<?php

class Reminder_model extends CI_Model
{
    public function get_all_reminders()
    {
        $this->db->order_by('T06_TARIKH', 'DESC');
        $query = $this->db->get("EV_T07_NOTIFIKASI_PESANAN");
        return $query;
    }

    public function get_reminder($id_notifikasi)
    {
        $this->db->where('T06_ID_NOTIFIKASI', $id_notifikasi);
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        
        return false;
    }

    public function save_reminder($data)
    {
        try {
            // Check if record already exists
            $this->db->where('T06_ID_NOTIFIKASI', $data['T06_ID_NOTIFIKASI']);
            $existing = $this->db->get('EV_T07_NOTIFIKASI_PESANAN')->row();
            
            if ($existing) {
                // Update existing record
                $this->db->where('T06_ID_NOTIFIKASI', $data['T06_ID_NOTIFIKASI']);
                return $this->db->update('EV_T07_NOTIFIKASI_PESANAN', $data);
            } else {
                // Insert new record
                return $this->db->insert('EV_T07_NOTIFIKASI_PESANAN', $data);
            }
        } catch (Exception $e) {
            log_message('error', 'Error saving reminder: ' . $e->getMessage());
            return false;
        }
    }

    public function delete_reminder($id_notifikasi)
    {
        try {
            $this->db->where('T06_ID_NOTIFIKASI', $id_notifikasi);
            return $this->db->delete('EV_T07_NOTIFIKASI_PESANAN');
        } catch (Exception $e) {
            log_message('error', 'Error deleting reminder: ' . $e->getMessage());
            return false;
        }
    }

    public function update_reminder($id_notifikasi, $data)
    {
        try {
            $this->db->where('T06_ID_NOTIFIKASI', $id_notifikasi);
            return $this->db->update('EV_T07_NOTIFIKASI_PESANAN', $data);
        } catch (Exception $e) {
            log_message('error', 'Error updating reminder: ' . $e->getMessage());
            return false;
        }
    }

    public function get_reminder_stats()
    {
        $stats = [];
        
        // Total reminders
        $stats['total'] = $this->db->count_all('EV_T07_NOTIFIKASI_PESANAN');
        
        // Reminders due this month
        $current_month = date('M');
        $current_year = substr(date('Y'), -2);
        
        $this->db->where("UPPER(SUBSTR(T06_TAMAT_PESANAN, 4, 3)) = '$current_month'", null, false);
        $this->db->where("SUBSTR(T06_TAMAT_PESANAN, 8, 2) = '$current_year'", null, false);
        $stats['due_this_month'] = $this->db->count_all_results('EV_T07_NOTIFIKASI_PESANAN');
        
        // Overdue reminders (past due date)
        $today = date('d-M-y');
        $this->db->where("TO_DATE(T06_TAMAT_PESANAN, 'DD-MON-YY') < TO_DATE('$today', 'DD-MON-YY')", null, false);
        $stats['overdue'] = $this->db->count_all_results('EV_T07_NOTIFIKASI_PESANAN');
        
        // Recent uploads (this week)
        $week_ago = date('d-M-y', strtotime('-7 days'));
        $this->db->where("TO_DATE(T06_TARIKH, 'DD-MON-YY') >= TO_DATE('$week_ago', 'DD-MON-YY')", null, false);
        $stats['recent'] = $this->db->count_all_results('EV_T07_NOTIFIKASI_PESANAN');
        
        return $stats;
    }

    public function get_reminders_by_status($status = 'all')
    {
        $today = date('d-M-y');
        
        switch ($status) {
            case 'overdue':
                $this->db->where("TO_DATE(T06_TAMAT_PESANAN, 'DD-MON-YY') < TO_DATE('$today', 'DD-MON-YY')", null, false);
                break;
                
            case 'due_soon':
                $next_week = date('d-M-y', strtotime('+7 days'));
                $this->db->where("TO_DATE(T06_TAMAT_PESANAN, 'DD-MON-YY') BETWEEN TO_DATE('$today', 'DD-MON-YY') AND TO_DATE('$next_week', 'DD-MON-YY')", null, false);
                break;
                
            case 'future':
                $next_week = date('d-M-y', strtotime('+7 days'));
                $this->db->where("TO_DATE(T06_TAMAT_PESANAN, 'DD-MON-YY') > TO_DATE('$next_week', 'DD-MON-YY')", null, false);
                break;
                
            default:
                // Return all
                break;
        }
        
        $this->db->order_by('T06_TAMAT_PESANAN', 'ASC');
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        return $query;
    }

    public function search_reminders($search_term)
    {
        $this->db->group_start();
        $this->db->like('T06_NAMA_PEMBEKAL', $search_term);
        $this->db->or_like('T06_NOMBOR_PESANAN', $search_term);
        $this->db->or_like('T06_ID_NOTIFIKASI', $search_term);
        $this->db->or_like('T06_KOD_PEMBEKAL', $search_term);
        $this->db->or_like('T06_NO_ASET', $search_term);
        $this->db->group_end();
        
        $this->db->order_by('T06_TARIKH', 'DESC');
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        return $query;
    }

    public function get_suppliers()
    {
        $this->db->select('DISTINCT T06_NAMA_PEMBEKAL');
        $this->db->where('T06_NAMA_PEMBEKAL IS NOT NULL');
        $this->db->order_by('T06_NAMA_PEMBEKAL');
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        return $query->result();
    }

    public function get_summary_by_supplier()
    {
        $this->db->select('T06_NAMA_PEMBEKAL, COUNT(*) as total_orders, SUM(T06_JUMLAH_HARGA) as total_amount');
        $this->db->group_by('T06_NAMA_PEMBEKAL');
        $this->db->order_by('total_amount', 'DESC');
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        return $query->result();
    }

    public function get_monthly_summary($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $year_suffix = substr($year, -2);
        
        $this->db->select("
            SUBSTR(T06_TARIKH_PESANAN, 4, 3) as month,
            COUNT(*) as total_orders,
            SUM(T06_JUMLAH_HARGA) as total_amount
        ");
        $this->db->where("SUBSTR(T06_TARIKH_PESANAN, 8, 2) = '$year_suffix'", null, false);
        $this->db->group_by("SUBSTR(T06_TARIKH_PESANAN, 4, 3)");
        $this->db->order_by("TO_DATE('01-' || SUBSTR(T06_TARIKH_PESANAN, 4, 3) || '-' || SUBSTR(T06_TARIKH_PESANAN, 8, 2), 'DD-MON-YY')", null, false);
        
        $query = $this->db->get('EV_T07_NOTIFIKASI_PESANAN');
        return $query->result();
    }
}
?>