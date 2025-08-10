<?php
class Notifikasi_model extends CI_Model 
{
    public function get_all_notifikasi()
    {
        $this->db->order_by('T06_TARIKH_PESANAN', 'DESC');
        return $this->db->get("EV_T07_NOTIFIKASI_PESANAN")->result();
    }

    public function get_notifikasi_by_id($id)
    {
        $this->db->where("T06_ID_NOTIFIKASI", $id);
        return $this->db->get("EV_T07_NOTIFIKASI_PESANAN")->row();
    }

    public function insert_notifikasi($data)
    {
        // Handle Oracle DATE fields using TO_DATE
        $this->db->set("T06_NAMA_PEMBEKAL", $data["T06_NAMA_PEMBEKAL"]);
        
        // Sanitize phone number (only keep digits, dash, and plus)
        $no_telefon = preg_replace('/[^0-9\-\+]/', '', $data["T06_NOMBOR_TELEFON"]);
        $this->db->set("T06_NOMBOR_TELEFON", $no_telefon);
        
        $this->db->set("T06_NOMBOR_PESANAN", $data["T06_NOMBOR_PESANAN"]);
        
        $this->db->set("T06_TARIKH_PESANAN", "TO_DATE('{$data["T06_TARIKH_PESANAN"]}', 'DD-Mon-YYYY')", false);
        $this->db->set("T06_TAMAT_PESANAN", "TO_DATE('{$data["T06_TAMAT_PESANAN"]}', 'DD-Mon-YYYY')", false);
        
        // Cast to float to avoid ORA-01722 error
        $this->db->set("T06_JUMLAH_HARGA", (float) $data["T06_JUMLAH_HARGA"]);
        
        // Handle PDF file name
        if (!empty($data["T06_PDF_FILE"])) {
            $this->db->set("T06_PDF_FILE", $data["T06_PDF_FILE"]);
        } else {
            $this->db->set("T06_PDF_FILE", null);
        }
        
        $this->db->set("T06_TARIKH", "TO_DATE('{$data["T06_TARIKH"]}', 'DD-Mon-YYYY')", false);
        
        // Initialize status as null (Belum Terima)
        if (isset($data["T06_STATUS"])) {
            $this->db->set("T06_STATUS", $data["T06_STATUS"]);
        } else {
            $this->db->set("T06_STATUS", null);
        }
        
        return $this->db->insert("EV_T07_NOTIFIKASI_PESANAN");
    }

    public function update_notifikasi($id, $data)
    {
        // Convert dates to Oracle format if they exist in update data
        if (isset($data["T06_TARIKH_PESANAN"])) {
            $this->db->set("T06_TARIKH_PESANAN", "TO_DATE('{$data["T06_TARIKH_PESANAN"]}', 'DD-Mon-YYYY')", false);
            unset($data["T06_TARIKH_PESANAN"]);
        }
        
        if (isset($data["T06_TAMAT_PESANAN"])) {
            $this->db->set("T06_TAMAT_PESANAN", "TO_DATE('{$data["T06_TAMAT_PESANAN"]}', 'DD-Mon-YYYY')", false);
            unset($data["T06_TAMAT_PESANAN"]);
        }
        
        // Cast amount to float if exists
        if (isset($data["T06_JUMLAH_HARGA"])) {
            $data["T06_JUMLAH_HARGA"] = (float) $data["T06_JUMLAH_HARGA"];
        }
        
        // Sanitize phone if exists
        if (isset($data["T06_NOMBOR_TELEFON"])) {
            $data["T06_NOMBOR_TELEFON"] = preg_replace('/[^0-9\-\+]/', '', $data["T06_NOMBOR_TELEFON"]);
        }
        
        $this->db->where('T06_ID_NOTIFIKASI', $id);
        return $this->db->update('EV_T07_NOTIFIKASI_PESANAN', $data);
    }

    public function delete_notifikasi($id_notifikasi)
    {
        $this->db->where('T06_ID_NOTIFIKASI', $id_notifikasi);
        return $this->db->delete('EV_T07_NOTIFIKASI_PESANAN');
    }

    public function update_status($id, $status)
    {
        if (empty($id)) {
            return false;
        }
        
        $this->db->where('T06_ID_NOTIFIKASI', $id);
        return $this->db->update('EV_T07_NOTIFIKASI_PESANAN', ['T06_STATUS' => $status]);
    }

    // Get count of unreceived notifications (basic count)
    public function get_unreceived_count()
{
    $this->db->where('(T06_STATUS IS NULL OR T06_STATUS != \'Received\')');
    return $this->db->count_all_results('EV_T07_NOTIFIKASI_PESANAN');
}

    // FIXED: Count for sidebar notifications (3 days before deadline logic)
    public function count_sidebar_notifikasi()
{
    $today = date('Y-m-d');
    
    $sql = "
        SELECT COUNT(*) AS notification_count
        FROM EV_T07_NOTIFIKASI_PESANAN
        WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
        AND (
            -- Within 3 days of deadline
            (TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3)
            AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN)
            OR
            -- OR Overdue notifications
            (TO_DATE(?, 'YYYY-MM-DD') > T06_TAMAT_PESANAN)
            OR
            -- OR Short order-to-deadline period (<=3 days)
            (T06_TARIKH_PESANAN IS NOT NULL
            AND (T06_TAMAT_PESANAN - T06_TARIKH_PESANAN) <= 3)
        )
    ";
    
    $query = $this->db->query($sql, [$today, $today, $today]);
    
    return $query ? (int)$query->row()->NOTIFICATION_COUNT : 0;
}
public function count_near_deadline_notifications()
{
    $today = date('Y-m-d');
    
    $sql = "
        SELECT COUNT(*) AS notification_count
        FROM EV_T07_NOTIFIKASI_PESANAN
        WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
        AND (
            -- Within 3 days of deadline
            (TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3)
            OR
            -- OR if period between order date and deadline is <= 3 days
            (T06_TARIKH_PESANAN IS NOT NULL 
             AND (T06_TAMAT_PESANAN - T06_TARIKH_PESANAN) <= 3)
        )
    ";
    
    $query = $this->db->query($sql, [$today]);
    
    return $query ? (int)$query->row()->NOTIFICATION_COUNT : 0;
}
    
    // Get notifications that need alerts (for dashboard or detailed view)
    public function get_alert_notifications()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT *
            FROM EV_T07_NOTIFIKASI_PESANAN
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            AND TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3)
            AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN
            ORDER BY T06_TAMAT_PESANAN ASC
        ";
        
        $query = $this->db->query($sql, [$today, $today]);
        return $query ? $query->result() : [];
    }
    
    // Get overdue notifications (past deadline and not received)
    public function get_overdue_notifications()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT *
            FROM EV_T07_NOTIFIKASI_PESANAN
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            AND TO_DATE(?, 'YYYY-MM-DD') > T06_TAMAT_PESANAN
            ORDER BY T06_TAMAT_PESANAN ASC
        ";
        
        $query = $this->db->query($sql, [$today]);
        return $query ? $query->result() : [];
    }
    
    // Debug method to test date calculations
    public function debug_date_calculations()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT 
                T06_ID_NOTIFIKASI,
                T06_NAMA_PEMBEKAL,
                T06_TAMAT_PESANAN,
                T06_STATUS,
                (T06_TAMAT_PESANAN - TO_DATE(?, 'YYYY-MM-DD')) AS days_until_deadline,
                CASE 
                    WHEN TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3) AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN 
                    THEN 'ALERT' 
                    ELSE 'OK' 
                END AS alert_status
            FROM EV_T07_NOTIFIKASI_PESANAN
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            ORDER BY T06_TAMAT_PESANAN ASC
        ";
        
        $query = $this->db->query($sql, [$today, $today, $today]);
        return $query ? $query->result() : [];
    }
}