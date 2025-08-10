<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reject_notes_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get note for specific month and year
     */
    public function get_notes_by_date($year, $month = null)
    {
        $sql = "SELECT * FROM EV_T06_REJECT_NOTES 
                WHERE T06_YEAR_SELECTED = :year";
        
        if ($month) {
            $sql .= " AND T06_MONTH_SELECTED = :month";
        }
        
        $sql .= " ORDER BY T06_UPDATED_AT DESC";
        
        try {
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_bind_by_name($stmt, ':year', $year);
            if ($month) {
                oci_bind_by_name($stmt, ':month', $month);
            }
            oci_execute($stmt);
            
            $results = [];
            while ($row = oci_fetch_assoc($stmt)) {
                if (is_object($row['T06_NOTE_TEXT'])) {
                    $row['T06_NOTE_TEXT'] = $row['T06_NOTE_TEXT']->load();
                }
                $results[] = (object) $row;
            }
            oci_free_statement($stmt);
            
            return $results;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting notes: ' . $e->getMessage());
            return [];
        }
    }

    public function get_notes_by_year()
{
    return $this->db
        ->select('DISTINCT T06_MONTH_SELECTED, T06_YEAR_SELECTED')
        ->order_by('T06_YEAR_SELECTED', 'DESC')
        ->order_by('T06_MONTH_SELECTED', 'DESC')
        ->get('EV_T06_REJECT_NOTES')
        ->result();
}

    
    /**
     * Get all notes ordered by update date
     */
    public function get_all_notes() 
    {
        $sql = "SELECT * FROM EV_T06_REJECT_NOTES ORDER BY T06_UPDATED_AT DESC";
        
        try {
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_execute($stmt);
            
            $results = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $results[] = (object) $row;
            }
            oci_free_statement($stmt);
            
            return $results;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting all notes: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Save or update note for specific period
     */
    public function save_note($month, $year, $note_text, $created_by = null) 
    {
        // Handle empty values
        $month = $month ?: 'Semua Bulan';
        $year = $year ?: 'Semua Tahun';
        $period_key = $month . '-' . $year;
        
        // Check if note already exists
        $existing = $this->get_note($month, $year);
        
        try {
            if ($existing) {
                // Update existing note
                $sql = "UPDATE EV_T06_REJECT_NOTES 
                        SET T06_NOTE_TEXT = :note_text, 
                            T06_UPDATED_AT = SYSDATE 
                        WHERE T06_PERIOD_KEY = :period_key";
                
                $stmt = oci_parse($this->db->conn_id, $sql);
                oci_bind_by_name($stmt, ':note_text', $note_text);
                oci_bind_by_name($stmt, ':period_key', $period_key);
                
            } else {
                // Insert new note
                $sql = "INSERT INTO EV_T06_REJECT_NOTES 
                        (T06_MONTH_SELECTED, T06_YEAR_SELECTED, T06_PERIOD_KEY, T06_NOTE_TEXT, T06_CREATED_BY) 
                        VALUES (:month, :year, :period_key, :note_text, :created_by)";
                
                $stmt = oci_parse($this->db->conn_id, $sql);
                oci_bind_by_name($stmt, ':month', $month);
                oci_bind_by_name($stmt, ':year', $year);
                oci_bind_by_name($stmt, ':period_key', $period_key);
                oci_bind_by_name($stmt, ':note_text', $note_text);
                oci_bind_by_name($stmt, ':created_by', $created_by);
            }
            
            $result = oci_execute($stmt);
            oci_free_statement($stmt);
            
            return $result;
            
        } catch (Exception $e) {
            log_message('error', 'Error saving note: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete note by period key
     */
    public function delete_note($period_key) 
    {
        $sql = "DELETE FROM EV_T06_REJECT_NOTES WHERE T06_PERIOD_KEY = :period_key";
        
        try {
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_bind_by_name($stmt, ':period_key', $period_key);
            $result = oci_execute($stmt);
            oci_free_statement($stmt);
            
            return $result;
            
        } catch (Exception $e) {
            log_message('error', 'Error deleting note: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete note by month and year
     */
    public function delete_note_by_period($month, $year) 
    {
        $month = $month ?: 'Semua Bulan';
        $year = $year ?: 'Semua Tahun';
        $period_key = $month . '-' . $year;
        
        return $this->delete_note($period_key);
    }
    
    /**
     * Check if note exists for period
     */
    public function note_exists($month, $year) 
    {
        $note = $this->get_note($month, $year);
        return !empty($note);
    }
    
    /**
     * Get notes count
     */
    public function get_notes_count() 
    {
        $sql = "SELECT COUNT(*) as TOTAL FROM EV_T06_REJECT_NOTES";
        
        try {
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_execute($stmt);
            
            $row = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);
            
            return $row ? (int)$row['TOTAL'] : 0;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting notes count: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent notes (last 10)
     */
    public function get_recent_notes($limit = 10) 
    {
        $sql = "SELECT * FROM (
                    SELECT * FROM EV_T06_REJECT_NOTES 
                    ORDER BY T06_UPDATED_AT DESC
                ) WHERE ROWNUM <= :limit";
        
        try {
            $stmt = oci_parse($this->db->conn_id, $sql);
            oci_bind_by_name($stmt, ':limit', $limit);
            oci_execute($stmt);
            
            $results = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $results[] = (object) $row;
            }
            oci_free_statement($stmt);
            
            return $results;
            
        } catch (Exception $e) {
            log_message('error', 'Error getting recent notes: ' . $e->getMessage());
            return [];
        }
    }

    public function get_note($month, $year)
{
    // Pad single-digit month to '01', '02', etc.
    if (is_numeric($month) && strlen($month) === 1) {
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    }

    $this->db->where('T06_MONTH_SELECTED', $month);
    $this->db->where('T06_YEAR_SELECTED', $year);
    return $this->db->get('EV_T06_REJECT_NOTES')->row();
}

}
?>