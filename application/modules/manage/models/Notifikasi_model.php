<?php
class Notifikasi_model extends CI_Model 
{
    private $table = "EV_T07_NOTIFIKASI_PESANAN";

    // =============================================================================
    // BASIC CRUD OPERATIONS
    // =============================================================================

    /**
     * Get all notifications ordered by date
     */
    public function get_all_notifikasi()
    {
        $this->db->order_by('T06_TARIKH_PESANAN', 'DESC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get notification by ID
     */
    public function get_notifikasi_by_id($id)
    {
        $this->db->where("T06_ID_NOTIFIKASI", $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Insert new notification
     */
    public function insert_notifikasi($data)
    {
        // Set basic fields
        $this->db->set("T06_NAMA_PEMBEKAL", $data["T06_NAMA_PEMBEKAL"]);
        $this->db->set("T06_NOMBOR_TELEFON", $this->_sanitize_phone($data["T06_NOMBOR_TELEFON"]));
        $this->db->set("T06_NOMBOR_PESANAN", $data["T06_NOMBOR_PESANAN"]);
        $this->db->set("T06_JUMLAH_HARGA", (float) $data["T06_JUMLAH_HARGA"]);

        // Handle Oracle DATE fields
        $this->db->set("T06_TARIKH_PESANAN", "TO_DATE('{$data["T06_TARIKH_PESANAN"]}', 'DD-Mon-YYYY')", false);
        $this->db->set("T06_TAMAT_PESANAN", "TO_DATE('{$data["T06_TAMAT_PESANAN"]}', 'DD-Mon-YYYY')", false);
        $this->db->set("T06_TARIKH", "TO_DATE('{$data["T06_TARIKH"]}', 'DD-Mon-YYYY')", false);

        // Handle PDF file
        $this->db->set("T06_PDF_FILE", $data["T06_PDF_FILE"] ?? null);

        // Initialize status as null (Belum Terima)
        $this->db->set("T06_STATUS", $data["T06_STATUS"] ?? null);
        
        return $this->db->insert($this->table);
    }

    /**
     * Update existing notification
     */
    public function update_notifikasi($id, $data)
    {
        // Handle Oracle date fields
        if (isset($data["T06_TARIKH_PESANAN"])) {
            $this->db->set("T06_TARIKH_PESANAN", "TO_DATE('{$data["T06_TARIKH_PESANAN"]}', 'DD-Mon-YYYY')", false);
            unset($data["T06_TARIKH_PESANAN"]);
        }
        
        if (isset($data["T06_TAMAT_PESANAN"])) {
            $this->db->set("T06_TAMAT_PESANAN", "TO_DATE('{$data["T06_TAMAT_PESANAN"]}', 'DD-Mon-YYYY')", false);
            unset($data["T06_TAMAT_PESANAN"]);
        }
        
        // Sanitize numeric and text fields
        if (isset($data["T06_JUMLAH_HARGA"])) {
            $data["T06_JUMLAH_HARGA"] = (float) $data["T06_JUMLAH_HARGA"];
        }
        
        if (isset($data["T06_NOMBOR_TELEFON"])) {
            $data["T06_NOMBOR_TELEFON"] = $this->_sanitize_phone($data["T06_NOMBOR_TELEFON"]);
        }
        
        $this->db->where('T06_ID_NOTIFIKASI', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete notification
     */
    public function delete_notifikasi($id_notifikasi)
    {
        $this->db->where('T06_ID_NOTIFIKASI', $id_notifikasi);
        return $this->db->delete($this->table);
    }

    /**
     * Update notification status
     */
    public function update_status($id, $status)
    {
        if (empty($id)) {
            return false;
        }
        
        $this->db->where('T06_ID_NOTIFIKASI', $id);
        return $this->db->update($this->table, ['T06_STATUS' => $status]);
    }

    // =============================================================================
    // NOTIFICATION COUNTING METHODS
    // =============================================================================

    /**
     * Get basic count of unreceived notifications
     */
    public function get_unreceived_count()
    {
        $this->db->where('(T06_STATUS IS NULL OR T06_STATUS != \'Received\')');
        return $this->db->count_all_results($this->table);
    }

    /**
     * Count notifications for sidebar badge (main counting logic)
     * Shows notifications that need attention based on deadlines
     */
    public function count_sidebar_notifikasi()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT COUNT(*) AS notification_count
            FROM {$this->table}
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            AND (
                -- Current date is within 4 days before deadline
                (TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 4)
                AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN)
                OR
                -- Short-duration orders (≤ 4 days) that are not yet overdue
                (T06_TARIKH_PESANAN IS NOT NULL 
                 AND (T06_TAMAT_PESANAN - T06_TARIKH_PESANAN) <= 4
                 AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN)
            )
        ";
        
        $query = $this->db->query($sql, [$today, $today, $today]);
        return $query ? (int)$query->row()->NOTIFICATION_COUNT : 0;
    }

    /**
     * Alternative counting method (3-day window)
     */
    public function count_near_deadline_notifications()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT COUNT(*) AS notification_count
            FROM {$this->table}
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            AND (
                -- Within 3 days of deadline
                (TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3)
                AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN)
                OR
                -- Short-duration orders (≤ 3 days)
                (T06_TARIKH_PESANAN IS NOT NULL 
                 AND (T06_TAMAT_PESANAN - T06_TARIKH_PESANAN) <= 3
                 AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN)
            )
        ";
        
        $query = $this->db->query($sql, [$today, $today, $today]);
        return $query ? (int)$query->row()->NOTIFICATION_COUNT : 0;
    }

    // =============================================================================
    // DATA RETRIEVAL METHODS
    // =============================================================================

    /**
     * Get notifications that need immediate attention
     */
    public function get_alert_notifications()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            AND TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 3)
            AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN
            ORDER BY T06_TAMAT_PESANAN ASC
        ";
        
        $query = $this->db->query($sql, [$today, $today]);
        return $query ? $query->result() : [];
    }

    /**
     * Get overdue notifications (past deadline)
     */
    public function get_overdue_notifications()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            AND TO_DATE(?, 'YYYY-MM-DD') > T06_TAMAT_PESANAN
            ORDER BY T06_TAMAT_PESANAN ASC
        ";
        
        $query = $this->db->query($sql, [$today]);
        return $query ? $query->result() : [];
    }

    // =============================================================================
    // DEBUG AND TESTING METHODS
    // =============================================================================

    /**
     * Debug method for testing date calculations
     */
    public function debug_date_calculations()
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT 
                T06_ID_NOTIFIKASI,
                T06_NAMA_PEMBEKAL,
                TO_CHAR(T06_TARIKH_PESANAN, 'YYYY-MM-DD') as T06_TARIKH_PESANAN_FORMATTED,
                TO_CHAR(T06_TAMAT_PESANAN, 'YYYY-MM-DD') as T06_TAMAT_PESANAN_FORMATTED,
                T06_TARIKH_PESANAN,
                T06_TAMAT_PESANAN,
                T06_STATUS,
                (T06_TAMAT_PESANAN - T06_TARIKH_PESANAN) AS ORDER_TO_DEADLINE_DAYS,
                (T06_TAMAT_PESANAN - TO_DATE(?, 'YYYY-MM-DD')) AS DAYS_UNTIL_DEADLINE,
                CASE 
                    WHEN (TO_DATE(?, 'YYYY-MM-DD') >= (T06_TAMAT_PESANAN - 4) 
                         AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN)
                    THEN 'WITHIN_4_DAYS' 
                    WHEN (T06_TAMAT_PESANAN - T06_TARIKH_PESANAN) <= 4
                         AND TO_DATE(?, 'YYYY-MM-DD') <= T06_TAMAT_PESANAN
                    THEN 'SHORT_DURATION'
                    ELSE 'NO_ALERT'
                END AS ALERT_REASON
            FROM {$this->table}
            WHERE (T06_STATUS IS NULL OR T06_STATUS != 'Received')
            ORDER BY T06_TAMAT_PESANAN ASC
        ";
        
        $query = $this->db->query($sql, [$today, $today, $today, $today]);
        return $query ? $query->result() : [];
    }

    // =============================================================================
    // PRIVATE HELPER METHODS
    // =============================================================================

    /**
     * Sanitize phone number (keep only digits, dash, and plus)
     */
    private function _sanitize_phone($phone)
    {
        return preg_replace('/[^0-9\-\+]/', '', $phone);
    }
}