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
    public function get_note($month, $year) 
{
    $month = $month ?: 'Semua Bulan';
    $year = $year ?: 'Semua Tahun';

    if (is_numeric($month) && $month !== 'Semua Bulan') {
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    }

    $period_key = $month . '-' . $year;
    
    $sql = "SELECT * FROM EV_T06_REJECT_NOTES WHERE T06_PERIOD_KEY = :period_key";
    
    try {
        $stmt = oci_parse($this->db->conn_id, $sql);
        oci_bind_by_name($stmt, ':period_key', $period_key);
        oci_execute($stmt);
        
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        if ($row) {
            // Handle LOB
            if (is_object($row['T06_NOTE_TEXT'])) {
                $row['T06_NOTE_TEXT'] = $row['T06_NOTE_TEXT']->load();
            }

            if (isset($row['T06_UPDATED_AT']) && is_object($row['T06_UPDATED_AT'])) {
                $row['T06_UPDATED_AT'] = $row['T06_UPDATED_AT']->format('Y-m-d H:i:s');
            }

            return (object) $row;
        }

        return null;
    } catch (Exception $e) {
        log_message('error', 'Error getting note: ' . $e->getMessage());
        return null;
    }
}

public function get_recent_notes($limit = 5)
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
            // Handle LOB data
            if (is_object($row['T06_NOTE_TEXT'])) {
                $row['T06_NOTE_TEXT'] = $row['T06_NOTE_TEXT']->load();
            }
            
            // Handle date
            if (isset($row['T06_UPDATED_AT'])) {
                if (is_object($row['T06_UPDATED_AT'])) {
                    $row['T06_UPDATED_AT'] = $row['T06_UPDATED_AT']->format('Y-m-d H:i:s');
                }
                $row['T06_UPDATED_AT'] = $row['T06_UPDATED_AT'] ?? null;
            }
            
            $results[] = (object) $row;
        }
        oci_free_statement($stmt);
        
        return $results;
        
    } catch (Exception $e) {
        log_message('error', 'Error getting recent notes: ' . $e->getMessage());
        return [];
    }
}

public function get_notes_grouped_by_year()
{
    $sql = "
        SELECT DISTINCT 
            T06_MONTH_SELECTED, 
            T06_YEAR_SELECTED
        FROM 
            EV_T06_REJECT_NOTES
        WHERE 
            T06_MONTH_SELECTED IS NOT NULL AND T06_YEAR_SELECTED IS NOT NULL
        ORDER BY 
            T06_YEAR_SELECTED DESC, 
            T06_MONTH_SELECTED DESC
    ";

    return $this->db->query($sql)->result();
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
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);

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
     * Delete note by ID
     */
public function delete_note_by_id($id)
{
    $sql = "DELETE FROM EV_T06_REJECT_NOTES WHERE T06_NOTE_ID = :id";
    $stmt = oci_parse($this->db->conn_id, $sql);
    
    if (!$stmt) {
        $error = oci_error($this->db->conn_id);
        log_message('error', 'Parse error: '.$error['message']);
        return false;
    }
    
    oci_bind_by_name($stmt, ':id', $id);
    
    if (!oci_execute($stmt)) {
        $error = oci_error($stmt);
        log_message('error', 'Execute error: '.$error['message']);
        oci_free_statement($stmt);
        return false;
    }
    
    $rows = oci_num_rows($stmt);
    oci_free_statement($stmt);
    
    log_message('debug', "Deleted $rows row(s)");
    return ($rows > 0);
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

    public function get_notes_by_month_year($month, $year)
{
    $this->db->select('T06_NOTE_ID, T06_NOTE_TEXT, T06_UPDATED_AT'); // include all fields you need
    $this->db->from('EV_T06_REJECT_NOTES');
    $this->db->where('T06_MONTH_SELECTED', $month);
    $this->db->where('T06_YEAR_SELECTED', $year);
    $query = $this->db->get();
    return $query->result();
}

public function get_notes_by_year($year)
{
    $this->db->select('*');
    $this->db->from('EV_T06_REJECT_NOTES');
    $this->db->where('T06_YEAR_SELECTED', $year);
    $this->db->order_by('T06_MONTH_SELECTED', 'DESC');
    $query = $this->db->get();
    return $query->result();
}

public function get_note_by_id($id)
{
    $sql = "SELECT * FROM EV_T06_REJECT_NOTES WHERE T06_NOTE_ID = :id";
    $stmt = oci_parse($this->db->conn_id, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    return $row ? (object) $row : null;
}

public function update_note($id, $month, $year, $note_text)
{
    $sql = "UPDATE EV_T06_REJECT_NOTES 
            SET T06_NOTE_TEXT = :note_text, 
                T06_MONTH_SELECTED = :month, 
                T06_YEAR_SELECTED = :year, 
                T06_UPDATED_AT = SYSDATE 
            WHERE T06_NOTE_ID = :id";
    $stmt = oci_parse($this->db->conn_id, $sql);
    oci_bind_by_name($stmt, ':note_text', $note_text);
    oci_bind_by_name($stmt, ':month', $month);
    oci_bind_by_name($stmt, ':year', $year);
    oci_bind_by_name($stmt, ':id', $id);
    $result = oci_execute($stmt);
    oci_free_statement($stmt);
    return $result;
}

}
?>